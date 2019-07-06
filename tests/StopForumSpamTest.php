<?php

namespace nickurt\StopForumSpam\Tests;

use Orchestra\Testbench\TestCase;
use StopForumSpam;
use Event;
use Validator;

class StopForumSpamTest extends TestCase
{
    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Event' => \Illuminate\Support\Facades\Event::class,
            'StopForumSpam' => \nickurt\StopForumSpam\Facade::class
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \nickurt\StopForumSpam\ServiceProvider::class
        ];
    }

    /** @test */
    public function it_can_get_the_http_client()
    {
        $this->assertInstanceOf(\GuzzleHttp\Client::class, StopForumSpam::getClient());
    }

    /** @test */
    public function it_can_return_the_default_values()
    {
        $stopForumSpam = \StopForumSpam::getFacadeRoot();

        $this->assertSame('https://api.stopforumspam.org/api', $stopForumSpam->getApiUrl());
        $this->assertSame(10, $stopForumSpam->getFrequency());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_api_url()
    {
        $stopForumSpam = \StopForumSpam::setApiUrl('https://api-ppe.stopforumspam.org/api');

        $this->assertSame('https://api-ppe.stopforumspam.org/api', $stopForumSpam->getApiUrl());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_email()
    {
        $stopForumSpam = \StopForumSpam::setEmail('65egadnatl@liam.ur');

        $this->assertSame('65egadnatl@liam.ur', $stopForumSpam->getEmail());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_frequency()
    {
        $stopForumSpam = \StopForumSpam::setFrequency(90);

        $this->assertSame(90, $stopForumSpam->getFrequency());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_ip()
    {
        $stopForumSpam = \StopForumSpam::setIp('191.186.18.61');

        $this->assertSame('191.186.18.61', $stopForumSpam->getIp());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_username()
    {
        $stopForumSpam = \StopForumSpam::setUsername('argaiv');

        $this->assertSame('argaiv', $stopForumSpam->getUsername());
    }

    /** @test */
    public function it_can_work_with_app_instance()
    {
        $this->assertInstanceOf(\nickurt\StopForumSpam\StopForumSpam::class, app('StopForumSpam'));

        $this->assertInstanceOf(\nickurt\StopForumSpam\StopForumSpam::class, $this->app['StopForumSpam']);
    }

    /** @test */
    public function it_can_work_with_helper_function()
    {
        $this->assertInstanceOf(\nickurt\StopForumSpam\StopForumSpam::class, stopforumspam());
    }

    /** @test */
    public function it_will_fire_is_spam_email_event_by_a_spam_email_via_facade()
    {
        \Event::fake();

        \StopForumSpam::setClient(new \GuzzleHttp\Client([
            'handler' => new \GuzzleHttp\Handler\MockHandler([
                new \GuzzleHttp\Psr7\Response(200, [], '{"success":1,"email":{"lastseen":"2019-07-05 11:19:21","frequency":37096,"appears":1,"confidence":99.99}}')
            ]),
        ]))->setEmail('ltandage56@mail.ru')->isSpamEmail();

        \Event::assertDispatched(\nickurt\StopForumSpam\Events\IsSpamEmail::class, function ($e) {
            return ($e->email == 'ltandage56@mail.ru');
        });
    }

    /** @test */
    public function it_will_fire_is_spam_email_event_by_a_spam_email_via_validation_rule()
    {
        \Event::fake();

        \StopForumSpam::setClient(new \GuzzleHttp\Client([
            'handler' => new \GuzzleHttp\Handler\MockHandler([
                new \GuzzleHttp\Psr7\Response(200, [], '{"success":1,"email":{"lastseen":"2019-07-05 11:19:21","frequency":37096,"appears":1,"confidence":99.99}}')
            ]),
        ]));

        $rule = new \nickurt\StopForumSpam\Rules\IsSpamEmail(10);

        $this->assertFalse($rule->passes('email', 'ltandage56@mail.ru'));

        \Event::assertDispatched(\nickurt\StopForumSpam\Events\IsSpamEmail::class, function ($e) {
            return ($e->email == 'ltandage56@mail.ru');
        });
    }

    /** @test */
    public function it_will_fire_is_spam_ip_event_by_a_spam_ip_via_facade()
    {
        \Event::fake();

        \StopForumSpam::setClient(new \GuzzleHttp\Client([
            'handler' => new \GuzzleHttp\Handler\MockHandler([
                new \GuzzleHttp\Psr7\Response(200, [], '{"success":1,"ip":{"lastseen":"2019-07-05 11:23:03","frequency":255,"appears":1,"confidence":99.95,"delegated":"ua","country":"us","asn":36352}}')
            ]),
        ]))->setIp('193.201.224.246')->isSpamIp();

        \Event::assertDispatched(\nickurt\StopForumSpam\Events\IsSpamIp::class, function ($e) {
            return ($e->ip == '193.201.224.246');
        });
    }

    /** @test */
    public function it_will_fire_is_spam_ip_event_by_a_spam_ip_via_validation_rule()
    {
        \Event::fake();

        \StopForumSpam::setClient(new \GuzzleHttp\Client([
            'handler' => new \GuzzleHttp\Handler\MockHandler([
                new \GuzzleHttp\Psr7\Response(200, [], '{"success":1,"ip":{"lastseen":"2019-07-05 11:23:03","frequency":255,"appears":1,"confidence":99.95,"delegated":"ua","country":"us","asn":36352}}')
            ]),
        ]));

        $rule = new \nickurt\StopForumSpam\Rules\IsSpamIp(10);

        $this->assertFalse($rule->passes('ip', '193.201.224.246'));

        \Event::assertDispatched(\nickurt\StopForumSpam\Events\IsSpamIp::class, function ($e) {
            return ($e->ip == '193.201.224.246');
        });
    }

    /** @test */
    public function it_will_fire_is_spam_username_event_by_a_spam_username_via_facade()
    {
        \Event::fake();

        \StopForumSpam::setClient(new \GuzzleHttp\Client([
            'handler' => new \GuzzleHttp\Handler\MockHandler([
                new \GuzzleHttp\Psr7\Response(200, [], '{"success":1,"username":{"lastseen":"2019-06-03 15:13:16","frequency":15,"appears":1,"confidence":8.04}}')
            ]),
        ]))->setUsername('viagra')->isSpamUsername();

        \Event::assertDispatched(\nickurt\StopForumSpam\Events\IsSpamUsername::class, function ($e) {
            return ($e->username == 'viagra');
        });
    }

    /** @test */
    public function it_will_fire_is_spam_username_event_by_a_spam_username_via_validation_rule()
    {
        \Event::fake();

        \StopForumSpam::setClient(new \GuzzleHttp\Client([
            'handler' => new \GuzzleHttp\Handler\MockHandler([
                new \GuzzleHttp\Psr7\Response(200, [], '{"success":1,"username":{"lastseen":"2019-06-03 15:13:16","frequency":15,"appears":1,"confidence":8.04}}')
            ]),
        ]));

        $rule = new \nickurt\StopForumSpam\Rules\IsSpamUsername(10);

        $this->assertFalse($rule->passes('username', 'viagra'));

        \Event::assertDispatched(\nickurt\StopForumSpam\Events\IsSpamUsername::class, function ($e) {
            return ($e->username == 'viagra');
        });
    }

    /** @test */
    public function it_will_not_fire_is_spam_email_event_by_a_non_spam_email_via_facade()
    {
        \Event::fake();

        \StopForumSpam::setClient(new \GuzzleHttp\Client([
            'handler' => new \GuzzleHttp\Handler\MockHandler([
                new \GuzzleHttp\Psr7\Response(200, [], '{"success":1,"email":{"frequency":0,"appears":0}}')
            ]),
        ]))->setEmail('xrumertest@this.baddomain.com')->isSpamEmail();

        \Event::assertNotDispatched(\nickurt\StopForumSpam\Events\IsSpamEmail::class);
    }

    /** @test */
    public function it_will_not_fire_is_spam_email_event_by_a_non_spam_email_via_validation_rule()
    {
        \Event::fake();

        \StopForumSpam::setClient(new \GuzzleHttp\Client([
            'handler' => new \GuzzleHttp\Handler\MockHandler([
                new \GuzzleHttp\Psr7\Response(200, [], '{"success":1,"email":{"frequency":0,"appears":0}}')
            ]),
        ]));

        $rule = new \nickurt\StopForumSpam\Rules\IsSpamEmail(10);

        $this->assertTrue($rule->passes('email', 'xrumertest@this.baddomain.com'));

        \Event::assertNotDispatched(\nickurt\StopForumSpam\Events\IsSpamEmail::class);
    }

    /** @test */
    public function it_will_not_fire_is_spam_ip_event_by_a_non_spam_ip_via_facade()
    {
        \Event::fake();

        \StopForumSpam::setClient(new \GuzzleHttp\Client([
            'handler' => new \GuzzleHttp\Handler\MockHandler([
                new \GuzzleHttp\Psr7\Response(200, [], '{"success":1,"ip":{"frequency":0,"appears":0,"country":"us","asn":36352}}')
            ]),
        ]))->setIp('191.186.18.61')->isSpamIp();

        \Event::assertNotDispatched(\nickurt\StopForumSpam\Events\IsSpamIp::class);
    }

    /** @test */
    public function it_will_not_fire_is_spam_ip_event_by_a_non_spam_ip_via_validation_rule()
    {
        \Event::fake();

        \StopForumSpam::setClient(new \GuzzleHttp\Client([
            'handler' => new \GuzzleHttp\Handler\MockHandler([
                new \GuzzleHttp\Psr7\Response(200, [], '{"success":1,"ip":{"frequency":0,"appears":0,"country":"us","asn":36352}}')
            ]),
        ]));

        $rule = new \nickurt\StopForumSpam\Rules\IsSpamIp(10);

        $this->assertTrue($rule->passes('ip', '191.186.18.61'));

        \Event::assertNotDispatched(\nickurt\StopForumSpam\Events\IsSpamIp::class);
    }

    /** @test */
    public function it_will_not_fire_is_spam_username_event_by_a_non_spam_username_via_facade()
    {
        \Event::fake();

        \StopForumSpam::setClient(new \GuzzleHttp\Client([
            'handler' => new \GuzzleHttp\Handler\MockHandler([
                new \GuzzleHttp\Psr7\Response(200, [], '{"success":1,"username":{"frequency":0,"appears":0}}')
            ]),
        ]))->setUsername('stopforumspam')->IsSpamUsername();

        \Event::assertNotDispatched(\nickurt\StopForumSpam\Events\IsSpamUsername::class);
    }

    /** @test */
    public function it_will_not_fire_is_spam_username_event_by_a_non_spam_username_via_validation_rule()
    {
        \Event::fake();

        \StopForumSpam::setClient(new \GuzzleHttp\Client([
            'handler' => new \GuzzleHttp\Handler\MockHandler([
                new \GuzzleHttp\Psr7\Response(200, [], '{"success":1,"username":{"frequency":0,"appears":0}}')
            ]),
        ]))->setUsername('stopforumspam')->IsSpamUsername();

        $rule = new \nickurt\StopForumSpam\Rules\IsSpamUsername(10);

        $this->assertTrue($rule->passes('username', 'stopforumspam'));

        \Event::assertNotDispatched(\nickurt\StopForumSpam\Events\IsSpamUsername::class);
    }

    /** @test */
    public function it_will_throw_malformed_url_exception()
    {
        $this->expectException(\nickurt\StopForumSpam\Exception\MalformedURLException::class);

        \StopForumSpam::setApiUrl('malformed_url');
    }
}