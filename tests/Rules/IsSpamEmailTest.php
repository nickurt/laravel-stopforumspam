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

        Http::fake(['https://api.stopforumspam.org/api?email=anejost52@mail.ru&json' => Http::response('{"success":1,"email":{"value":"anejost52@mail.ru","lastseen":"2025-02-25 18:08:54","frequency":16,"appears":1,"confidence":78.05}}')]);

        $rule = new \nickurt\StopForumSpam\Rules\IsSpamEmail(10);

        $this->assertFalse($rule->passes('email', 'anejost52@mail.ru'));

        Event::assertDispatched(IsSpamEmail::class, function ($e) {
            $this->assertSame(16, $e->frequency);
            $this->assertSame('anejost52@mail.ru', $e->email);

            return true;
        });
    }

    public function test_it_will_not_fire_is_spam_email_event_by_a_non_spam_email_via_validation_rule()
    {
        Event::fake();

        Http::fake(['https://api.stopforumspam.org/api?email=xrumertest@this.baddomain.com&json' => Http::response('{"success":1,"email":{"value":"xrumertest@this.baddomain.com","frequency":0,"appears":0}}')]);

        $rule = new \nickurt\StopForumSpam\Rules\IsSpamEmail(10);

        $this->assertTrue($rule->passes('email', 'xrumertest@this.baddomain.com'));

        Event::assertNotDispatched(IsSpamEmail::class);
    }
}
