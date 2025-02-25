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

        Http::fake(['https://api.stopforumspam.org/api?ip=193.201.224.246&json' => Http::response('{"success":1,"ip":{"value":"193.201.224.246","frequency":255,"appears":1,"lastseen":"2025-02-25 18:20:15","confidence":99.95,"blacklisted":1}}')]);

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

        Http::fake(['https://api.stopforumspam.org/api?ip=191.186.18.61&json' => Http::response('{"success":1,"ip":{"value":"191.186.18.61","frequency":0,"appears":0,"asn":28573,"country":"br"}}')]);

        $rule = new \nickurt\StopForumSpam\Rules\IsSpamIp(10);

        $this->assertTrue($rule->passes('ip', '191.186.18.61'));

        Event::assertNotDispatched(IsSpamIp::class);
    }
}
