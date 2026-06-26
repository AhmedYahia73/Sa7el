<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;

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

        // Rate Limiters
        RateLimiter::for('forget_password_check', function (Request $request) {
            return Limit::perMinutes(5, 3)->by('check_forget_password:' . $request->ip())->response(function () {
                return response()->json([
                    'errors' => 'Too many attempts. Please try again after 5 minutes.'
                ], 429);
            });
        });

        RateLimiter::for('forget_password_update', function (Request $request) {
            return Limit::perMinutes(5, 3)->by('update_password:' . $request->ip())->response(function () {
                return response()->json([
                    'errors' => 'Too many attempts. Please try again after 5 minutes.'
                ], 429);
            });
        });

        // Scramble API Docs
        Scramble::registerApi('public', [
            'api_path' => 'api',
            'info' => ['title' => 'Public API', 'version' => '1.0.0'],
        ]);

        Scramble::registerApi('admin', [
            'api_path' => 'admin',
            'info' => ['title' => 'Admin API', 'version' => '1.0.0'],
        ]);

        Scramble::registerApi('village', [
            'api_path' => 'village',
            'info' => ['title' => 'Village API', 'version' => '1.0.0'],
        ]);

        Scramble::registerApi('provider', [
            'api_path' => 'provider',
            'info' => ['title' => 'Provider API', 'version' => '1.0.0'],
        ]);

        Scramble::registerApi('maintenance_provider', [
            'api_path' => 'maintenance_provider',
            'info' => ['title' => 'Maintenance Provider API', 'version' => '1.0.0'],
        ]);

        Scramble::registerApi('user', [
            'api_path' => 'user',
            'info' => ['title' => 'User API', 'version' => '1.0.0'],
        ]);

        Scramble::registerApi('security', [
            'api_path' => 'security',
            'info' => ['title' => 'Security API', 'version' => '1.0.0'],
        ]);

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
