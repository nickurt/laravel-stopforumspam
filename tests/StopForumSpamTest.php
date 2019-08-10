<?php

namespace nickurt\StopForumSpam\Tests;

use Event;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Application;
use nickurt\StopForumSpam\Events\IsSpamEmail;
use nickurt\StopForumSpam\Events\IsSpamIp;
use nickurt\StopForumSpam\Events\IsSpamUsername;
use nickurt\StopForumSpam\Exception\MalformedURLException;
use nickurt\StopForumSpam\Facade;
use nickurt\StopForumSpam\ServiceProvider;
use Orchestra\Testbench\TestCase;
use StopForumSpam;
use Validator;

class StopForumSpamTest extends TestCase
{
    /** @var \nickurt\StopForumSpam\StopForumSpam */
    protected $stopForumSpam;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        /** @var \nickurt\StopForumSpam\StopForumSpam stopFormSpam */
        $this->stopForumSpam = StopForumSpam::getFacadeRoot();
    }

    /** @test */
    public function it_can_get_the_http_client()
    {
        $this->assertInstanceOf(Client::class, $this->stopForumSpam->getClient());
    }

    /** @test */
    public function it_can_return_the_default_values()
    {
        $this->assertSame('https://api.stopforumspam.org/api', $this->stopForumSpam->getApiUrl());
        $this->assertSame(10, $this->stopForumSpam->getFrequency());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_api_url()
    {
        $this->stopForumSpam->setApiUrl('https://api-ppe.stopforumspam.org/api');

        $this->assertSame('https://api-ppe.stopforumspam.org/api', $this->stopForumSpam->getApiUrl());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_email()
    {
        $this->stopForumSpam->setEmail('65egadnatl@liam.ur');

        $this->assertSame('65egadnatl@liam.ur', $this->stopForumSpam->getEmail());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_frequency()
    {
        $this->stopForumSpam->setFrequency(90);

        $this->assertSame(90, $this->stopForumSpam->getFrequency());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_ip()
    {
        $this->stopForumSpam->setIp('191.186.18.61');

        $this->assertSame('191.186.18.61', $this->stopForumSpam->getIp());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_username()
    {
        $this->stopForumSpam->setUsername('argaiv');

        $this->assertSame('argaiv', $this->stopForumSpam->getUsername());
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
        Event::fake();

        $this->stopForumSpam->setClient(new Client([
            'handler' => new MockHandler([
                new Response(200, [], '{"success":1,"email":{"lastseen":"2019-07-05 11:19:21","frequency":37096,"appears":1,"confidence":99.99}}')
            ]),
        ]))->setEmail('ltandage56@mail.ru')->isSpamEmail();

        $this->assertSame('https://api.stopforumspam.org/api?email=ltandage56@mail.ru&json', (string)$this->stopForumSpam->getClient()->getConfig()['handler']->getLastRequest()->getUri());

        Event::assertDispatched(IsSpamEmail::class, function ($e) {
            $this->assertSame(37096, $e->frequency);
            $this->assertSame('ltandage56@mail.ru', $e->email);

            return true;
        });
    }

    /** @test */
    public function it_will_fire_is_spam_email_event_by_a_spam_email_via_validation_rule()
    {
        Event::fake();

        $this->stopForumSpam->setClient(new Client([
            'handler' => new MockHandler([
                new Response(200, [], '{"success":1,"email":{"lastseen":"2019-07-05 11:19:21","frequency":37096,"appears":1,"confidence":99.99}}')
            ]),
        ]));

        $rule = new \nickurt\StopForumSpam\Rules\IsSpamEmail(10);

        $this->assertFalse($rule->passes('email', 'ltandage56@mail.ru'));

        Event::assertDispatched(IsSpamEmail::class, function ($e) {
            $this->assertSame(37096, $e->frequency);
            $this->assertSame('ltandage56@mail.ru', $e->email);

            return true;
        });
    }

    /** @test */
    public function it_will_fire_is_spam_ip_event_by_a_spam_ip_via_facade()
    {
        Event::fake();

        $this->stopForumSpam->setClient(new Client([
            'handler' => new MockHandler([
                new Response(200, [], '{"success":1,"ip":{"lastseen":"2019-07-05 11:23:03","frequency":255,"appears":1,"confidence":99.95,"delegated":"ua","country":"us","asn":36352}}')
            ]),
        ]))->setIp('193.201.224.246')->isSpamIp();

        $this->assertSame('https://api.stopforumspam.org/api?ip=193.201.224.246&json', (string)$this->stopForumSpam->getClient()->getConfig()['handler']->getLastRequest()->getUri());

        Event::assertDispatched(IsSpamIp::class, function ($e) {
            $this->assertSame(255, $e->frequency);
            $this->assertSame('193.201.224.246', $e->ip);

            return true;
        });
    }

    /** @test */
    public function it_will_fire_is_spam_ip_event_by_a_spam_ip_via_validation_rule()
    {
        Event::fake();

        $this->stopForumSpam->setClient(new Client([
            'handler' => new MockHandler([
                new Response(200, [], '{"success":1,"ip":{"lastseen":"2019-07-05 11:23:03","frequency":255,"appears":1,"confidence":99.95,"delegated":"ua","country":"us","asn":36352}}')
            ]),
        ]));

        $rule = new \nickurt\StopForumSpam\Rules\IsSpamIp(10);

        $this->assertFalse($rule->passes('ip', '193.201.224.246'));

        Event::assertDispatched(IsSpamIp::class, function ($e) {
            $this->assertSame(255, $e->frequency);
            $this->assertSame('193.201.224.246', $e->ip);

            return true;
        });
    }

    /** @test */
    public function it_will_fire_is_spam_username_event_by_a_spam_username_via_facade()
    {
        Event::fake();

        $this->stopForumSpam->setClient(new Client([
            'handler' => new MockHandler([
                new Response(200, [], '{"success":1,"username":{"lastseen":"2019-06-03 15:13:16","frequency":15,"appears":1,"confidence":8.04}}')
            ]),
        ]))->setUsername('viagra')->isSpamUsername();

        $this->assertSame('https://api.stopforumspam.org/api?username=viagra&json', (string)$this->stopForumSpam->getClient()->getConfig()['handler']->getLastRequest()->getUri());

        Event::assertDispatched(IsSpamUsername::class, function ($e) {
            $this->assertSame(15, $e->frequency);
            $this->assertSame('viagra', $e->username);

            return true;
        });
    }

    /** @test */
    public function it_will_fire_is_spam_username_event_by_a_spam_username_via_validation_rule()
    {
        Event::fake();

        $this->stopForumSpam->setClient(new Client([
            'handler' => new MockHandler([
                new Response(200, [], '{"success":1,"username":{"lastseen":"2019-06-03 15:13:16","frequency":15,"appears":1,"confidence":8.04}}')
            ]),
        ]));

        $rule = new \nickurt\StopForumSpam\Rules\IsSpamUsername(10);

        $this->assertFalse($rule->passes('username', 'viagra'));

        Event::assertDispatched(IsSpamUsername::class, function ($e) {
            $this->assertSame(15, $e->frequency);
            $this->assertSame('viagra', $e->username);

            return true;
        });
    }

    /** @test */
    public function it_will_not_fire_is_spam_email_event_by_a_non_spam_email_via_facade()
    {
        Event::fake();

        $this->stopForumSpam->setClient(new Client([
            'handler' => new MockHandler([
                new Response(200, [], '{"success":1,"email":{"frequency":0,"appears":0}}')
            ]),
        ]))->setEmail('xrumertest@this.baddomain.com')->isSpamEmail();

        Event::assertNotDispatched(IsSpamEmail::class);
    }

    /** @test */
    public function it_will_not_fire_is_spam_email_event_by_a_non_spam_email_via_validation_rule()
    {
        Event::fake();

        $this->stopForumSpam->setClient(new Client([
            'handler' => new MockHandler([
                new Response(200, [], '{"success":1,"email":{"frequency":0,"appears":0}}')
            ]),
        ]));

        $rule = new \nickurt\StopForumSpam\Rules\IsSpamEmail(10);

        $this->assertTrue($rule->passes('email', 'xrumertest@this.baddomain.com'));

        Event::assertNotDispatched(IsSpamEmail::class);
    }

    /** @test */
    public function it_will_not_fire_is_spam_ip_event_by_a_non_spam_ip_via_facade()
    {
        Event::fake();

        $this->stopForumSpam->setClient(new Client([
            'handler' => new MockHandler([
                new Response(200, [], '{"success":1,"ip":{"frequency":0,"appears":0,"country":"us","asn":36352}}')
            ]),
        ]))->setIp('191.186.18.61')->isSpamIp();

        Event::assertNotDispatched(IsSpamIp::class);
    }

    /** @test */
    public function it_will_not_fire_is_spam_ip_event_by_a_non_spam_ip_via_validation_rule()
    {
        Event::fake();

        $this->stopForumSpam->setClient(new Client([
            'handler' => new MockHandler([
                new Response(200, [], '{"success":1,"ip":{"frequency":0,"appears":0,"country":"us","asn":36352}}')
            ]),
        ]));

        $rule = new \nickurt\StopForumSpam\Rules\IsSpamIp(10);

        $this->assertTrue($rule->passes('ip', '191.186.18.61'));

        Event::assertNotDispatched(IsSpamIp::class);
    }

    /** @test */
    public function it_will_not_fire_is_spam_username_event_by_a_non_spam_username_via_facade()
    {
        Event::fake();

        $this->stopForumSpam->setClient(new Client([
            'handler' => new MockHandler([
                new Response(200, [], '{"success":1,"username":{"frequency":0,"appears":0}}')
            ]),
        ]))->setUsername('stopforumspam')->IsSpamUsername();

        Event::assertNotDispatched(IsSpamUsername::class);
    }

    /** @test */
    public function it_will_not_fire_is_spam_username_event_by_a_non_spam_username_via_validation_rule()
    {
        Event::fake();

        $this->stopForumSpam->setClient(new Client([
            'handler' => new MockHandler([
                new Response(200, [], '{"success":1,"username":{"frequency":0,"appears":0}}')
            ]),
        ]))->setUsername('stopforumspam')->IsSpamUsername();

        $rule = new \nickurt\StopForumSpam\Rules\IsSpamUsername(10);

        $this->assertTrue($rule->passes('username', 'stopforumspam'));

        Event::assertNotDispatched(IsSpamUsername::class);
    }

    /** @test */
    public function it_will_throw_malformed_url_exception()
    {
        $this->expectException(MalformedURLException::class);

        $this->stopForumSpam->setApiUrl('malformed_url');
    }

    /**
     * @param Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Event' => \Illuminate\Support\Facades\Event::class,
            'StopForumSpam' => Facade::class
        ];
    }

    /**
     * @param Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class
        ];
    }
}
