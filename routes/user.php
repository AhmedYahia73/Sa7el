<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\api\User\Property\PropertyController;
use App\Http\Controllers\api\User\Visit\VisitController;
use App\Http\Controllers\api\User\Maintenance\MaintenanceController;


Route::middleware(['auth:sanctum', 'IsUser'])->group(function(){
    Route::controller(PropertyController::class)->prefix('property')
    ->group(function() {
        Route::get('/', 'my_property'); 
        Route::post('/add', 'add_property'); 
    });

    Route::controller(VisitController::class)->prefix('visitor')
    ->group(function() {
        Route::get('/create_qr_code', 'create_qr_code'); 
        Route::get('/create_code', 'create_code'); 
    });

    Route::controller(MaintenanceController::class)->prefix('maintenance_request')
    ->group(function() {
        Route::get('/', 'maintenance_lists'); 
        Route::post('/add', 'maintenance_request'); 
    });

    Route::controller(MaintenanceController::class)->prefix('maintenance_request')
    ->group(function() {
        Route::post('/add', 'add_report'); 
    });
});