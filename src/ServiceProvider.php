<?php

namespace nickurt\StopForumSpam;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/Resources/Lang', 'stopforumspam');

        $this->publishes([
            __DIR__.'/Resources/Lang' => resource_path('lang/vendor/stopforumspam'),
        ], 'lang');

        $this->publishes([
            __DIR__.'/../config/stopforumspam.php' => config_path('stopforumspam.php'),
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
}
