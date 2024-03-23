<?php

namespace nickurt\StopForumSpam\tests\Rules;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use nickurt\StopForumSpam\Events\IsSpamUsername;
use nickurt\StopForumSpam\tests\TestCase;

class IsSpamUsernameTest extends TestCase
{
    public function test_it_will_fire_is_spam_username_event_by_a_spam_username_via_validation_rule()
    {
        Event::fake();

        Http::fake(['https://api.stopforumspam.org/api?username=viagra&json' => Http::response('{"success":1,"username":{"lastseen":"2020-03-03 15:24:36","frequency":22,"appears":1,"confidence":54.03}}')]);

        $rule = new \nickurt\StopForumSpam\Rules\IsSpamUsername(10);

        $this->assertFalse($rule->passes('username', 'viagra'));

        Event::assertDispatched(IsSpamUsername::class, function ($e) {
            $this->assertSame(22, $e->frequency);
            $this->assertSame('viagra', $e->username);

            return true;
        });
    }

    public function test_it_will_not_fire_is_spam_username_event_by_a_non_spam_username_via_validation_rule()
    {
        Event::fake();

        Http::fake(['https://api.stopforumspam.org/api?username=stopforumspam&json' => Http::response('{"success":1,"username":{"frequency":0,"appears":0}}')]);

        $rule = new \nickurt\StopForumSpam\Rules\IsSpamUsername(10);

        $this->assertTrue($rule->passes('username', 'stopforumspam'));

        Event::assertNotDispatched(IsSpamUsername::class);
    }
}
