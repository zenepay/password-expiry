<?php

namespace Zenepay\PasswordExpiry;

use Illuminate\Support\ServiceProvider;


class PasswordExpiryServiceProvider extends ServiceProvider
{

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../config/password_history.php' => config_path('password_history.php')], 'config');
        }
        $this->publishes([
            __DIR__ . '/../resources/views/auth/password-expired.blade.php' => resource_path('view/auth/password-expired.blade.php'),
        ], 'view');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'zenepay');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/password_history.php',
            'password_history'
        );
    }
}
