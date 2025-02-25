<?php

namespace nickurt\StopForumSpam\tests;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use nickurt\StopForumSpam\Events\IsSpamEmail;
use nickurt\StopForumSpam\Events\IsSpamIp;
use nickurt\StopForumSpam\Events\IsSpamUsername;
use nickurt\StopForumSpam\Exception\MalformedURLException;
use nickurt\StopForumSpam\Facade as StopForumSpam;

class StopForumSpamTest extends TestCase
{
    /** @var \nickurt\StopForumSpam\StopForumSpam */
    protected $stopForumSpam;

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        /** @var \nickurt\StopForumSpam\StopForumSpam stopForumSpam */
        $this->stopForumSpam = StopForumSpam::getFacadeRoot();
    }

    public function test_it_can_return_the_default_values()
    {
        $this->assertSame('https://api.stopforumspam.org/api', $this->stopForumSpam->getApiUrl());
        $this->assertSame(10, $this->stopForumSpam->getFrequency());
    }

    public function test_it_can_set_a_custom_value_for_the_api_url()
    {
        $this->stopForumSpam->setApiUrl('https://api-ppe.stopforumspam.org/api');

        $this->assertSame('https://api-ppe.stopforumspam.org/api', $this->stopForumSpam->getApiUrl());
    }

    public function test_it_can_set_a_custom_value_for_the_email()
    {
        $this->stopForumSpam->setEmail('65egadnatl@liam.ur');

        $this->assertSame('65egadnatl@liam.ur', $this->stopForumSpam->getEmail());
    }

    public function test_it_can_set_a_custom_value_for_the_frequency()
    {
        $this->stopForumSpam->setFrequency(90);

        $this->assertSame(90, $this->stopForumSpam->getFrequency());
    }

    public function test_it_can_set_a_custom_value_for_the_ip()
    {
        $this->stopForumSpam->setIp('191.186.18.61');

        $this->assertSame('191.186.18.61', $this->stopForumSpam->getIp());
    }

    public function test_it_can_set_a_custom_value_for_the_username()
    {
        $this->stopForumSpam->setUsername('argaiv');

        $this->assertSame('argaiv', $this->stopForumSpam->getUsername());
    }

    public function test_it_can_work_with_app_instance()
    {
        $this->assertInstanceOf(\nickurt\StopForumSpam\StopForumSpam::class, app('StopForumSpam'));

        $this->assertInstanceOf(\nickurt\StopForumSpam\StopForumSpam::class, $this->app['StopForumSpam']);
    }

    public function test_it_can_work_with_helper_function()
    {
        $this->assertInstanceOf(\nickurt\StopForumSpam\StopForumSpam::class, stopforumspam());
    }

    public function test_it_will_fire_correctly_based_on_frequency_is_spam_email_event_via_facade()
    {
        Event::fake();

        Http::fake(['https://api.stopforumspam.org/api?email=adelaidaconnelly911@07stees.online&json' => Http::response('{"success":1,"email":{"value":"adelaidaconnelly911@07stees.online","lastseen":"2025-02-25 18:29:05","frequency":255,"appears":1,"confidence":99.95,"blacklisted":1}}')]);

        Event::assertNotDispatched(IsSpamEmail::class);

        $this->assertTrue($this->stopForumSpam->setEmail('adelaidaconnelly911@07stees.online')->setFrequency(0)->isSpamEmail());
        $this->assertSame(0, $this->stopForumSpam->getFrequency());
        $this->assertSame('adelaidaconnelly911@07stees.online', $this->stopForumSpam->getEmail());

        Event::assertDispatched(IsSpamEmail::class, 1);
    }

    public function test_it_will_fire_is_spam_email_event_by_a_spam_email_via_facade()
    {
        Event::fake();

        Http::fake(['https://api.stopforumspam.org/api?email=anejost52@mail.ru&json' => Http::response('{"success":1,"email":{"value":"anejost52@mail.ru","lastseen":"2025-02-25 18:08:54","frequency":16,"appears":1,"confidence":78.05}}')]);

        $this->stopForumSpam->setEmail('anejost52@mail.ru')->isSpamEmail();

        Event::assertDispatched(IsSpamEmail::class, function ($e) {
            $this->assertSame(16, $e->frequency);
            $this->assertSame('anejost52@mail.ru', $e->email);

            return true;
        });
    }

    public function test_it_will_fire_is_spam_ip_event_by_a_spam_ip_via_facade()
    {
        Event::fake();

        Http::fake(['https://api.stopforumspam.org/api?ip=193.201.224.246&json' => Http::response('{"success":1,"ip":{"value":"193.201.224.246","lastseen":"2025-02-25 18:34:43","frequency":255,"appears":1,"confidence":99.95,"blacklisted":1}}')]);

        $this->stopForumSpam->setIp('193.201.224.246')->isSpamIp();

        Event::assertDispatched(IsSpamIp::class, function ($e) {
            $this->assertSame(255, $e->frequency);
            $this->assertSame('193.201.224.246', $e->ip);

            return true;
        });
    }

    public function test_it_will_fire_is_spam_username_event_by_a_spam_username_via_facade()
    {
        Event::fake();

        Http::fake(['https://api.stopforumspam.org/api?username=Johnnyloots&json' => Http::response('{"success":1,"username":{"value":"Johnnyloots","lastseen":"2025-02-25 18:06:47","frequency":17,"appears":1,"confidence":79.07}}')]);

        $this->stopForumSpam->setUsername('Johnnyloots')->isSpamUsername();

        Event::assertDispatched(IsSpamUsername::class, function ($e) {
            $this->assertSame(17, $e->frequency);
            $this->assertSame('Johnnyloots', $e->username);

            return true;
        });
    }

    public function test_it_will_not_fire_is_spam_email_event_by_a_non_spam_email_via_facade()
    {
        Event::fake();

        Http::fake(['https://api.stopforumspam.org/api?email=xrumertest@this.baddomain.com&json' => Http::response('{"success":1,"email":{"value":"xrumertest@this.baddomain.com","frequency":0,"appears":0}}')]);

        $this->stopForumSpam->setEmail('xrumertest@this.baddomain.com')->isSpamEmail();

        Event::assertNotDispatched(IsSpamEmail::class);
    }

    public function test_it_will_not_fire_is_spam_ip_event_by_a_non_spam_ip_via_facade()
    {
        Event::fake();

        Http::fake(['https://api.stopforumspam.org/api?ip=191.186.18.61&json' => Http::response('{"success":1,"ip":{"value":"191.186.18.61","frequency":0,"appears":0,"country":"br","asn":28573}}')]);

        $this->stopForumSpam->setIp('191.186.18.61')->isSpamIp();

        Event::assertNotDispatched(IsSpamIp::class);
    }

    public function test_it_will_not_fire_is_spam_username_event_by_a_non_spam_username_via_facade()
    {
        Event::fake();

        Http::fake(['https://api.stopforumspam.org/api?username=stopforumspam&json' => Http::response('{"success":1,"username":{"value":"stopforumspam","frequency":0,"appears":0}}')]);

        $this->stopForumSpam->setUsername('stopforumspam')->IsSpamUsername();

        Event::assertNotDispatched(IsSpamUsername::class);
    }

    public function test_it_will_throw_malformed_url_exception()
    {
        $this->expectException(MalformedURLException::class);

        $this->stopForumSpam->setApiUrl('malformed_url');
    }
}
