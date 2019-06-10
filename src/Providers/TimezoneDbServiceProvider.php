<?php

namespace Khatfield\LaravelTimezoneDb\Providers;

use Illuminate\Support\ServiceProvider;
use Khatfield\LaravelTimezoneDb\TimezoneDb;

class TimezoneDbServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $config = realpath(__DIR__ . '/..') . '/config/timezonedb.php';
        $this->mergeConfigFrom($config, 'timezonedb');

        $this->app->singleton(TimezoneDb::class, function($app)
        {
            $timezonedb = new TimezoneDb($app['config']);

            return $timezonedb;
        });

        $this->app->alias(TimezoneDb::class, 'timezonedb');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $config = realpath(__DIR__ . '/..') . '/config/ytel.php';
        $this->publishes(
            [
                $config => config_path('timezonedb.php')
            ], 'timezonedb-config'
        );
    }

    /**
     * @return array
     */
    public function provides()
    {
        return ['timezonedb', TimezoneDb::class];
    }
}
