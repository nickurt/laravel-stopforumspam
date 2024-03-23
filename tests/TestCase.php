<?php

namespace nickurt\StopForumSpam\tests;

use Illuminate\Contracts\Foundation\Application;
use nickurt\StopForumSpam\Facade;
use nickurt\StopForumSpam\ServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * @param  Application  $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Event' => \Illuminate\Support\Facades\Event::class,
            'StopForumSpam' => Facade::class,
        ];
    }

    /**
     * @param  Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }
}
