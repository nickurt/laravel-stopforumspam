<?php

namespace nickurt\StopForumSpam\Tests;

use Orchestra\Testbench\TestCase;
use StopForumSpam;

class StopForumSpamTest extends TestCase
{
    /** @test */
    public function test_it_can_get_default_values()
    {
        $sfs = new \nickurt\StopForumSpam\StopForumSpam();

        $this->assertSame('https://api.stopforumspam.org/api', $sfs->getApiUrl());
        $this->assertNull($sfs->getIp());
        $this->assertNull($sfs->getEmail());
        $this->assertNull($sfs->getUsername());
        $this->assertSame(10, $sfs->getFrequency());
    }

    /** @test */
    public function test_it_can_set_custom_values()
    {
        $sfs = (new \nickurt\StopForumSpam\StopForumSpam())
            ->setApiUrl('https://internal.api.stopforumspam.org/api')
            ->setIp('8.8.8.8')
            ->setEmail('nickurt@users.noreply.github.com')
            ->setUsername('nickurt')
            ->setFrequency(100);

        $this->assertSame('https://internal.api.stopforumspam.org/api', $sfs->getApiUrl());
        $this->assertSame('8.8.8.8', $sfs->getIp());
        $this->assertSame('nickurt@users.noreply.github.com', $sfs->getEmail());
        $this->assertSame('nickurt', $sfs->getUsername());
        $this->assertSame(100, $sfs->getFrequency());
    }

    /** @test */
    public function test_it_can_work_with_helper()
    {
        $this->assertTrue(function_exists('stopforumspam'));

        $this->assertInstanceOf(\nickurt\StopForumSpam\StopForumSpam::class, stopforumspam());
    }

    /** @test */
    public function test_it_can_work_with_container()
    {
        $this->assertInstanceOf(\nickurt\StopForumSpam\StopForumSpam::class, $this->app['StopForumSpam']);
    }

    /** @test */
    public function test_it_can_work_with_facade()
    {
        $this->assertSame('nickurt\StopForumSpam\Facade', (new \ReflectionClass(StopForumSpam::class))->getName());

        $this->assertSame('https://api.stopforumspam.org/api', StopForumSpam::getApiUrl());
        $this->assertNull(StopForumSpam::getIp());
        $this->assertNull(StopForumSpam::getEmail());
        $this->assertNull(StopForumSpam::getUsername());
        $this->assertSame(10, StopForumSpam::getFrequency());
    }

    /** @test */
    public function test_it_will_work_with_validation_rule_is_spam_ip()
    {
        $val1 = \Validator::make(['sfs' => 'sfs'], ['sfs' => ['required', new \nickurt\StopForumSpam\Rules\IsSpamIp('185.38.14.171', 10)]]);

        $this->assertFalse($val1->passes());
        $this->assertSame(1, count($val1->messages()->get('sfs')));
        $this->assertSame('It is currently not possible to register with your specified information, please try later again', $val1->messages()->first('sfs'));

        $val2 = \Validator::make(['sfs' => 'sfs'], ['sfs' => ['required', new \nickurt\StopForumSpam\Rules\IsSpamIp('185.38.14.171', 50000)]]);

        $this->assertTrue($val2->passes());
        $this->assertSame(0, count($val2->messages()->get('sfs')));

        $val3 = \Validator::make(['sfs' => 'sfs'], ['sfs' => ['required', new \nickurt\StopForumSpam\Rules\IsSpamIp('8.8.8.8', 10)]]);

        $this->assertTrue($val3->passes());
        $this->assertSame(0, count($val3->messages()->get('sfs')));
    }

    /** @test */
    public function test_it_will_work_with_validation_rule_is_spam_username()
    {
        $val1 = \Validator::make(['sfs' => 'sfs'], ['sfs' => ['required', new \nickurt\StopForumSpam\Rules\IsSpamUsername('viagra', 10)]]);

        $this->assertFalse($val1->passes());
        $this->assertSame(1, count($val1->messages()->get('sfs')));
        $this->assertSame('It is currently not possible to register with your specified information, please try later again', $val1->messages()->first('sfs'));

        $val2 = \Validator::make(['sfs' => 'sfs'], ['sfs' => ['required', new \nickurt\StopForumSpam\Rules\IsSpamUsername('viagra', 50000)]]);

        $this->assertTrue($val2->passes());
        $this->assertSame(0, count($val2->messages()->get('sfs')));
    }

    /** @test */
    public function test_it_will_work_with_validation_rule_is_spam_email()
    {
        $val1 = \Validator::make(['sfs' => 'sfs'], ['sfs' => ['required', new \nickurt\StopForumSpam\Rules\IsSpamEmail('xrumertest@this.baddomain.com', 10)]]);

        $this->assertFalse($val1->passes());
        $this->assertSame(1, count($val1->messages()->get('sfs')));
        $this->assertSame('It is currently not possible to register with your specified information, please try later again', $val1->messages()->first('sfs'));

        $val2 = \Validator::make(['sfs' => 'sfs'], ['sfs' => ['required', new \nickurt\StopForumSpam\Rules\IsSpamEmail('xrumertest@this.baddomain.com', 50000)]]);

        $this->assertTrue($val2->passes());
        $this->assertSame(0, count($val2->messages()->get('sfs')));
    }

    /**
     * @test
     * @expectedException \nickurt\StopForumSpam\Exception\MalformedURLException
     */
    public function test_it_will_throw_malformed_url_exception()
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