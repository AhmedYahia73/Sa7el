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
     */public function boot(): void
{
    // السماح بالوصول على السيرفر
    \Illuminate\Support\Facades\Gate::define('viewApiDocs', function ($user = null) {
        return true;
    });

    // تسجيل الـ Main API
    Scramble::registerApi('main-api')->routes(function ($route) {
        // تأخذ كل المسارات التي تبدأ بـ api ولكن لا تحتوي على user أو admin
        return str_starts_with($route->uri, 'api/') 
            && !str_contains($route->uri, 'user') 
            && !str_contains($route->uri, 'admin');
    })->afterOpenApiGenerated(function (OpenApi $openApi) {
        $openApi->info->title('Main API Documentation');
    });

    // تسجيل الـ User API
    Scramble::registerApi('user-api')->routes(function ($route) {
        return str_starts_with($route->uri, 'user') || str_starts_with($route->uri, 'user');
    })->afterOpenApiGenerated(function (OpenApi $openApi) {
        $openApi->info->title('User API Documentation');
    });

    // تسجيل الـ Admin API
    Scramble::registerApi('admin-api')->routes(function ($route) {
        return str_starts_with($route->uri, 'admin') || str_starts_with($route->uri, 'admin');
    })->afterOpenApiGenerated(function (OpenApi $openApi) {
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
