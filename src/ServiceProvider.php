<?php

namespace nickurt\StopForumSpam;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('nickurt\StopForumSpam\StopForumSpam', function ($app) {
            return new StopForumSpam;
        });

        $this->app->alias('nickurt\StopForumSpam\StopForumSpam', 'StopForumSpam');
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../src/Resources/Lang', 'stopforumspam');

        $this->publishes([
            __DIR__.'/../src/Resources/Lang' => resource_path('lang/vendor/stopforumspam'),
        ], 'config');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['nickurt\StopForumSpam\StopForumSpam', 'StopForumSpam'];
    }
}
