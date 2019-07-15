<?php

namespace TeamZac\TexasComptroller;

use Illuminate\Support\ServiceProvider;

class TexasComptrollerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('texas-comptroller.php'),
            ], 'config');

            $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'texas-comptroller');

        // Register the main class to use with the facade
        $this->app->singleton('texas-comptroller', function () {
            return new TexasComptroller;
        });
    }
}
