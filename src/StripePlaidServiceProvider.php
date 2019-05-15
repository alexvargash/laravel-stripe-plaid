<?php

namespace AlexVargash\LaravelStripePlaid;

use Illuminate\Support\ServiceProvider;
use AlexVargash\LaravelStripePlaid\StripePlaid;

class StripePlaidServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/stripe-plaid.php' => base_path('config/stripe-plaid.php')
        ], 'config');
    }

    public function register()
    {
        $this->app->bind('stripe-plaid', function() {
            return new StripePlaid();
        });

        $this->mergeConfigFrom(__DIR__ . '/../config/stripe-plaid.php', 'stripe-plaid');
    }
}
