<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\api\SuperAdmin\zones\ZoneController;
use App\Http\Controllers\api\SuperAdmin\village\VillageController;
use App\Http\Controllers\api\SuperAdmin\village\VillageGallaryController;
use App\Http\Controllers\api\SuperAdmin\appartment_type\AppartmentTypeController;

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
});