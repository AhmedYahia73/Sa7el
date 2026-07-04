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
use Illuminate\Support\Facades\Event;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Apple\AppleExtendSocialite;

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
        Event::listen(
            SocialiteWasCalled::class,
            AppleExtendSocialite::class
        );
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
        $apis = ['public', 'admin', 'village', 'provider', 'maintenance_provider', 'user', 'security'];
        $paths = [
            'public' => 'api',
            'admin' => 'admin',
            'village' => 'village',
            'provider' => 'provider',
            'maintenance_provider' => 'maintenance_provider',
            'user' => 'user',
            'security' => 'security',
        ];
        $titles = [
            'public' => 'Public API',
            'admin' => 'Admin API',
            'village' => 'Village API',
            'provider' => 'Provider API',
            'maintenance_provider' => 'Maintenance Provider API',
            'user' => 'User API',
            'security' => 'Security API',
        ];

        foreach ($apis as $name) {
            Scramble::registerApi($name, [
                'api_path' => $paths[$name],
                'info' => ['title' => $titles[$name], 'version' => '1.0.0'],
            ])->afterOpenApiGenerated(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer')
                );
            });
        }

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
