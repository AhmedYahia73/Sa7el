<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
 
use App\Providers\gates\Gates;
use App\Providers\gates\AdminGates;

use Illuminate\Support\Facades\Validator;
use App\Rules\Base64Image;

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
        AdminGates::defineGates();

        Validator::extend('base64image', function ($attribute, $value, $parameters, $validator) {
            $rule = new Base64Image();

            $failed = false;
            $rule->validate($attribute, $value, function () use (&$failed) {
                $failed = true;
            });
        return !$failed;
    });
    }
}
