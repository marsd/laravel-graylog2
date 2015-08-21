<?php
namespace AbsoluteSoftware\Graylog2;

use Illuminate\Support\ServiceProvider;

class Graylog2ServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bindShared('graylog2', function($app) {
            return new Graylog2();
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/graylog2.php' => config_path('graylog2.php'),
        ]);
    }

    public function provides()
    {
        return [
            'graylog2',
        ];
    }
}