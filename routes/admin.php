<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\api\SuperAdmin\zones\ZoneController;
use App\Http\Controllers\api\SuperAdmin\village\VillageController;
use App\Http\Controllers\api\SuperAdmin\village\VillageGallaryController;
use App\Http\Controllers\api\SuperAdmin\village\VillageAdminController;
use App\Http\Controllers\api\SuperAdmin\appartment_type\AppartmentTypeController;
use App\Http\Controllers\api\SuperAdmin\service_type\ServiceTypeController;
use App\Http\Controllers\api\SuperAdmin\users\UserController;
use App\Http\Controllers\api\SuperAdmin\Provider\ProviderController;
use App\Http\Controllers\api\SuperAdmin\Provider\ProviderAdminController;
use App\Http\Controllers\api\SuperAdmin\Provider\ProviderGalleryController;

Route::middleware(['auth:sanctum', 'IsAdmin'])->group(function(){
    Route::controller(ZoneController::class)->prefix('zone')
    ->group(function() {
        Route::get('/', 'view');
        Route::get('/item/{id}', 'zone');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });
    
    Route::controller(VillageController::class)->prefix('village')
    ->group(function() {
        Route::get('/', 'view');
        Route::get('/item/{id}', 'village');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });
    
    Route::controller(VillageGallaryController::class)->prefix('village_gallery')
    ->group(function() {
        Route::get('/{id}', 'view');
        Route::put('/status/{id}', 'status');
        Route::post('/add/{id}', 'create'); 
        Route::delete('/delete/{id}', 'delete');
    });
    
    Route::controller(VillageAdminController::class)->prefix('village_admin')
    ->group(function() {
        Route::get('/{id}', 'view');
        Route::get('/item/{id}', 'admin');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });
    
    Route::controller(AppartmentTypeController::class)->prefix('appartment_type')
    ->group(function() {
        Route::get('/', 'view');
        Route::get('/item/{id}', 'appartment_type');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });
    
    Route::controller(UserController::class)->prefix('user')
    ->group(function() {
        Route::get('/', 'view');
        Route::get('/item/{id}', 'user');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });
    
    Route::controller(ServiceTypeController::class)->prefix('service_type')
    ->group(function() {
        Route::get('/', 'view');
        Route::get('/item/{id}', 'service_type');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });
    
    Route::controller(ProviderController::class)->prefix('provider')
    ->group(function() {
        Route::get('/', 'view');
        Route::get('/item/{id}', 'provider');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });
    
    Route::controller(ProviderGalleryController::class)->prefix('provider_gallary')
    ->group(function() {
        Route::get('/{id}', 'view');
        Route::put('/status/{id}', 'status');
        Route::post('/add/{id}', 'create');
        Route::delete('/delete/{id}', 'delete');
    });
});