<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\VillageMiddleware;
use App\Http\Middleware\SecurityMiddleware;
use App\Http\Middleware\ProviderMiddleware;
use App\Http\Middleware\UserMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function(){
            Route::middleware('api')
            ->prefix('admin')
            ->name('admin.')
            ->group(base_path('routes/admin.php'));
            Route::middleware('api')
            ->prefix('provider')
            ->name('provider.')
            ->group(base_path('routes/provider.php'));
            Route::middleware('api')
            ->prefix('user')
            ->name('user.')
            ->group(base_path('routes/user.php'));
            Route::middleware('api')
            ->prefix('village')
            ->name('village.')
            ->group(base_path('routes/village.php'));
            Route::middleware('api')
            ->prefix('security')
            ->name('security.')
            ->group(base_path('routes/security.php'));
            Route::middleware('api')
            ->prefix('maintenance_provider')
            ->name('maintenance_provider.')
            ->group(base_path('routes/maintenance_provider.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'IsAdmin' => AdminMiddleware::class,
            'IsUser' => UserMiddleware::class,
            'IsVillage' => VillageMiddleware::class,
            'IsSecurity' => SecurityMiddleware::class,
            'IsProvider' => ProviderMiddleware::class,
            'IsMaintenanceProvider' => MaintenanceProviderMiddleware::class,
        ]);
        $middleware->redirectGuestsTo(function (Request $request) {
           if (!$request->is('api/*')) {
               return response()->json(['errors' => 'you must login', 400]);
           } 
       });
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
