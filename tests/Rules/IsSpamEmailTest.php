<?php

namespace nickurt\StopForumSpam\tests\Rules;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use nickurt\StopForumSpam\Events\IsSpamEmail;
use nickurt\StopForumSpam\tests\TestCase;

class IsSpamEmailTest extends TestCase
{
    public function test_it_will_fire_is_spam_email_event_by_a_spam_email_via_validation_rule()
    {
        Event::fake();

        Http::fake(['https://api.stopforumspam.org/api?email=ltandage56@mail.ru&json' => Http::response('{"success":1,"email":{"lastseen":"2019-11-01 14:52:36","frequency":24346,"appears":1,"confidence":97.79}}')]);

        $rule = new \nickurt\StopForumSpam\Rules\IsSpamEmail(10);

        $this->assertFalse($rule->passes('email', 'ltandage56@mail.ru'));

        Event::assertDispatched(IsSpamEmail::class, function ($e) {
            $this->assertSame(24346, $e->frequency);
            $this->assertSame('ltandage56@mail.ru', $e->email);

            return true;
        });
    }

    public function test_it_will_not_fire_is_spam_email_event_by_a_non_spam_email_via_validation_rule()
    {
        Event::fake();

        Http::fake(['https://api.stopforumspam.org/api?email=xrumertest@this.baddomain.com&json' => Http::response('{"success":1,"email":{"frequency":0,"appears":0}}')]);

        $rule = new \nickurt\StopForumSpam\Rules\IsSpamEmail(10);

        $this->assertTrue($rule->passes('email', 'xrumertest@this.baddomain.com'));

        Event::assertNotDispatched(IsSpamEmail::class);
    }
}
