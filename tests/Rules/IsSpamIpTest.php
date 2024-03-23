<?php

namespace nickurt\StopForumSpam\tests\Rules;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use nickurt\StopForumSpam\Events\IsSpamIp;
use nickurt\StopForumSpam\tests\TestCase;

class IsSpamIpTest extends TestCase
{
    public function test_it_will_fire_is_spam_ip_event_by_a_spam_ip_via_validation_rule()
    {
        Event::fake();

        Http::fake(['https://api.stopforumspam.org/api?ip=193.201.224.246&json' => Http::response('{"success":1,"ip":{"lastseen":"2020-03-12 20:17:51","frequency":255,"appears":1,"confidence":99.95,"delegated":"ua","country":"ua","asn":null}}')]);

        $rule = new \nickurt\StopForumSpam\Rules\IsSpamIp(10);

        $this->assertFalse($rule->passes('ip', '193.201.224.246'));

        Event::assertDispatched(IsSpamIp::class, function ($e) {
            $this->assertSame(255, $e->frequency);
            $this->assertSame('193.201.224.246', $e->ip);

            return true;
        });
    }

    public function test_it_will_not_fire_is_spam_ip_event_by_a_non_spam_ip_via_validation_rule()
    {
        Event::fake();

        Http::fake(['https://api.stopforumspam.org/api?ip=191.186.18.61&json' => Http::response('{"success":1,"ip":{"frequency":0,"appears":0,"country":"br","asn":28573}}')]);

        $rule = new \nickurt\StopForumSpam\Rules\IsSpamIp(10);

        $this->assertTrue($rule->passes('ip', '191.186.18.61'));

        Event::assertNotDispatched(IsSpamIp::class);
    }
}
