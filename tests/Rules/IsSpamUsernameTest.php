<?php

namespace nickurt\StopForumSpam\tests\Rules;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Event;
use nickurt\StopForumSpam\Events\IsSpamUsername;
use nickurt\StopForumSpam\tests\TestCase;

class IsSpamUsernameTest extends TestCase
{
    /** @test */
    public function it_will_fire_is_spam_username_event_by_a_spam_username_via_validation_rule()
    {
        Event::fake();

        \nickurt\StopForumSpam\Facade::setClient(new Client([
            'handler' => new MockHandler([
                new Response(200, [], '{"success":1,"username":{"lastseen":"2020-03-03 15:24:36","frequency":22,"appears":1,"confidence":54.03}}')
            ]),
        ]));

        $rule = new \nickurt\StopForumSpam\Rules\IsSpamUsername(10);

        $this->assertFalse($rule->passes('username', 'viagra'));

        Event::assertDispatched(IsSpamUsername::class, function ($e) {
            $this->assertSame(22, $e->frequency);
            $this->assertSame('viagra', $e->username);

            return true;
        });
    }

    /** @test */
    public function it_will_not_fire_is_spam_username_event_by_a_non_spam_username_via_validation_rule()
    {
        Event::fake();

        \nickurt\StopForumSpam\Facade::setClient(new Client([
            'handler' => new MockHandler([
                new Response(200, [], '{"success":1,"username":{"frequency":0,"appears":0}}')
            ]),
        ]))->setUsername('stopforumspam')->IsSpamUsername();

        $rule = new \nickurt\StopForumSpam\Rules\IsSpamUsername(10);

        $this->assertTrue($rule->passes('username', 'stopforumspam'));

        Event::assertNotDispatched(IsSpamUsername::class);
    }
}
