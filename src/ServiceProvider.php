<?php

namespace Alexmg86\LaravelSubQuery;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__.'/../config/laravel-sub-query.php';

    public function boot()
    {
        $this->publishes([
            self::CONFIG_PATH => config_path('laravel-sub-query.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'laravel-sub-query'
        );

        $this->app->bind('laravel-sub-query', function () {
            return new LaravelSubQuery();
        });
    }
}
