<?php
namespace Manadinho\Lucent;

use Illuminate\Support\ServiceProvider;

/**
 * Class LucentServiceProvider
 * @package Manadinho\Lucent\LucentServiceProvider
 * 
 * @author Muhammad Imran Israr (mimranisrar6@gmail.com)
 */
class LucentServiceProvider extends ServiceProvider 
{
    public function boot(): void
    {
        // Publishing the configuration file
        $this->publishes([
            __DIR__.'/../config/lucent.php' => config_path('lucent.php'),
        ]);
    }

    public function register(): void
    {
        $this->app->singleton('Lucent', function ($app) {
            return new Handler();
        });
    }
}
