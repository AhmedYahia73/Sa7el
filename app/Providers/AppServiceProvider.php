<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
 
use App\Providers\gates\Gates;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gates::defineGates();
        Gate::define('Admin_Admin', function ($user) { 
            if (!$user->provider_only) {
                return true;
            }
            return false;
        });
    }
}
