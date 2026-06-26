<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

use App\Providers\gates\Gates;
use App\Providers\gates\AdminGates;

use Illuminate\Support\Facades\Validator;
use App\Rules\Base64Image;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;

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
        Scramble::registerApi('main-api', [
            'api_path' => 'api', 
        ])->afterOpenApiGenerated(function (OpenApi $openApi) {
            $openApi->info->title('Main API Documentation');
        });

        Scramble::registerApi('user-api', [
            'api_path' => 'api/user',
        ])->afterOpenApiGenerated(function (OpenApi $openApi) {
            $openApi->info->title('User API Documentation');
        });

        Scramble::registerApi('admin-api', [
            'api_path' => 'api/admin',
        ])->afterOpenApiGenerated(function (OpenApi $openApi) {
            $openApi->info->title('Admin API Documentation');
        });
        Gates::defineGates();
        AdminGates::defineGates();

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
