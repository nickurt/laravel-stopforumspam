<?php

namespace nickurt\StopForumSpam\Tests;

use Orchestra\Testbench\TestCase;
use StopForumSpam;
use Event;
use Validator;

class StopForumSpamTest extends TestCase
{
    /** @test */
    public function it_can_get_default_values()
    {
        $sfs = new \nickurt\StopForumSpam\StopForumSpam();

        $this->assertSame('https://api.stopforumspam.org/api', $sfs->getApiUrl());
        $this->assertNull($sfs->getIp());
        $this->assertNull($sfs->getEmail());
        $this->assertNull($sfs->getUsername());
        $this->assertSame(10, $sfs->getFrequency());
    }

    /** @test */
    public function it_can_set_custom_values()
    {
        $sfs = (new \nickurt\StopForumSpam\StopForumSpam())
            ->setApiUrl('https://internal.api.stopforumspam.org/api')
            ->setIp('185.38.14.171')
            ->setEmail('xrumertest@this.baddomain.com')
            ->setUsername('viagra')
            ->setFrequency(100);

        $this->assertSame('https://internal.api.stopforumspam.org/api', $sfs->getApiUrl());
        $this->assertSame('185.38.14.171', $sfs->getIp());
        $this->assertSame('xrumertest@this.baddomain.com', $sfs->getEmail());
        $this->assertSame('viagra', $sfs->getUsername());
        $this->assertSame(100, $sfs->getFrequency());
    }

    /** @test */
    public function it_can_work_with_helper()
    {
        $this->assertTrue(function_exists('stopforumspam'));

        $this->assertInstanceOf(\nickurt\StopForumSpam\StopForumSpam::class, stopforumspam());
    }

    /** @test */
    public function it_can_work_with_container()
    {
        $this->assertInstanceOf(\nickurt\StopForumSpam\StopForumSpam::class, $this->app['StopForumSpam']);
    }

    /** @test */
    public function it_can_work_with_facade()
    {
        $this->assertSame('nickurt\StopForumSpam\Facade', (new \ReflectionClass(StopForumSpam::class))->getName());

        $this->assertSame('https://api.stopforumspam.org/api', StopForumSpam::getApiUrl());
        $this->assertNull(StopForumSpam::getIp());
        $this->assertNull(StopForumSpam::getEmail());
        $this->assertNull(StopForumSpam::getUsername());
        $this->assertSame(10, StopForumSpam::getFrequency());
    }

    /** @test */
    public function it_will_work_with_validation_rule_is_spam_ip()
    {
        $val1 = Validator::make(['sfs' => 'sfs'], ['sfs' => ['required', new \nickurt\StopForumSpam\Rules\IsSpamIp('185.38.14.171', 10)]]);

        $this->assertFalse($val1->passes());
        $this->assertSame(1, count($val1->messages()->get('sfs')));
        $this->assertSame('It is currently not possible to register with your specified information, please try later again', $val1->messages()->first('sfs'));

        $val2 = Validator::make(['sfs' => 'sfs'], ['sfs' => ['required', new \nickurt\StopForumSpam\Rules\IsSpamIp('185.38.14.171', 50000)]]);

        $this->assertTrue($val2->passes());
        $this->assertSame(0, count($val2->messages()->get('sfs')));
    }

    /** @test */
    public function it_will_work_with_validation_rule_is_spam_username()
    {
        $val1 = Validator::make(['sfs' => 'sfs'], ['sfs' => ['required', new \nickurt\StopForumSpam\Rules\IsSpamUsername('viagra', 10)]]);

        $this->assertFalse($val1->passes());
        $this->assertSame(1, count($val1->messages()->get('sfs')));
        $this->assertSame('It is currently not possible to register with your specified information, please try later again', $val1->messages()->first('sfs'));

        $val2 = Validator::make(['sfs' => 'sfs'], ['sfs' => ['required', new \nickurt\StopForumSpam\Rules\IsSpamUsername('viagra', 50000)]]);

        $this->assertTrue($val2->passes());
        $this->assertSame(0, count($val2->messages()->get('sfs')));
    }

    /** @test */
    public function it_will_work_with_validation_rule_is_spam_email()
    {
        $val1 = Validator::make(['sfs' => 'sfs'], ['sfs' => ['required', new \nickurt\StopForumSpam\Rules\IsSpamEmail('xrumertest@this.baddomain.com', 10)]]);

        $this->assertFalse($val1->passes());
        $this->assertSame(1, count($val1->messages()->get('sfs')));
        $this->assertSame('It is currently not possible to register with your specified information, please try later again', $val1->messages()->first('sfs'));

        $val2 = Validator::make(['sfs' => 'sfs'], ['sfs' => ['required', new \nickurt\StopForumSpam\Rules\IsSpamEmail('xrumertest@this.baddomain.com', 50000)]]);

        $this->assertTrue($val2->passes());
        $this->assertSame(0, count($val2->messages()->get('sfs')));
    }

    /** @test */
    public function it_will_fire_an_event_by_valid_spam()
    {
        Event::fake();

        $sfs = (new \nickurt\StopForumSpam\StopForumSpam());

        $isSpamIp = $sfs->setIp('185.38.14.171')->isSpamIp();

        Event::assertDispatched(\nickurt\StopForumSpam\Events\IsSpamIp::class, function($e) use ($sfs) {
            return $e->ip === $sfs->getIp();
        });

        $isSpamUsername = $sfs->setUsername('viagra')->isSpamUsername();

        Event::assertDispatched(\nickurt\StopForumSpam\Events\IsSpamUsername::class, function($e) use ($sfs) {
            return $e->username === $sfs->getUsername();
        });

        $isSpamEmail = $sfs->setEmail('xrumertest@this.baddomain.com')->isSpamEmail();

        Event::assertDispatched(\nickurt\StopForumSpam\Events\IsSpamEmail::class, function($e) use ($sfs) {
            return $e->email === $sfs->getEmail();
        });
    }

    /** @test */
    public function it_will_not_fire_an_event_by_invalid_spam()
    {
        Event::fake();

        $sfs = (new \nickurt\StopForumSpam\StopForumSpam());

        $isSpamIp = $sfs->setIp('185.38.14.171')->setFrequency(50000)->isSpamIp();

        Event::assertNotDispatched(\nickurt\StopForumSpam\Events\IsSpamIp::class);

        $isSpamUsername = $sfs->setUsername('viagra')->setFrequency(50000)->isSpamUsername();

        Event::assertNotDispatched(\nickurt\StopForumSpam\Events\IsSpamUsername::class);

        $isSpamEmail = $sfs->setEmail('xrumertest@this.baddomain.com')->setFrequency(50000)->isSpamEmail();

        Event::assertNotDispatched(\nickurt\StopForumSpam\Events\IsSpamEmail::class);
    }
    
    /**
     * @test
     * @expectedException \nickurt\StopForumSpam\Exception\MalformedURLException
     */
    public function it_will_throw_malformed_url_exception()
    {
        $sfs = (new \nickurt\StopForumSpam\StopForumSpam())
            ->setApiUrl('malformed_url');
    }

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
}