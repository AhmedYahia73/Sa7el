<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\api\Village\Owner\OwnerController;
use App\Http\Controllers\api\Village\Pools\PoolController;
use App\Http\Controllers\api\Village\Beach\BeachController;
use App\Http\Controllers\api\Village\Service\ServiceController;
use App\Http\Controllers\api\Village\Problem\ProblemController;
use App\Http\Controllers\api\Village\Maintenance\MaintenanceController;
use App\Http\Controllers\api\Village\Visitor\VisitorController;
use App\Http\Controllers\api\Village\Gate\GateController;
use App\Http\Controllers\api\Village\Security\SecurityController;
use App\Http\Controllers\api\Village\MaintenanceFeez\MaintenanceFeezController;
use App\Http\Controllers\api\Village\Appartments\AppartmentController;

Route::middleware(['auth:sanctum', 'IsVillage'])->group(function(){
    Route::controller(OwnerController::class)->prefix('owner')
    ->group(function() {
        Route::get('/', 'view');
        Route::get('/item/{id}', 'owner');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });

    Route::controller(MaintenanceFeezController::class)->prefix('maintenance_feez')
    ->group(function() {
        Route::get('/', 'view');
        Route::get('/year', 'view_year');
        Route::post('/add_payment', 'add_payment');
    });

    Route::controller(AppartmentController::class)->prefix('appartment')
    ->group(function() {
        Route::get('/', 'view');
        Route::put('/create_code', 'create_code');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });

    Route::controller(PoolController::class)->prefix('pool')
    ->group(function() {
        Route::get('/', 'view');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });

    Route::controller(BeachController::class)->prefix('beach')
    ->group(function() {
        Route::get('/', 'view');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });

    Route::controller(ServiceController::class)->prefix('service')
    ->group(function() {
        Route::get('/', 'view');
    });

    Route::controller(ProblemController::class)->prefix('problem')
    ->group(function() {
        Route::get('/', 'view');
        Route::put('/status/{id}', 'status');
    });

    Route::controller(MaintenanceController::class)->prefix('maintenance')
    ->group(function() {
        Route::get('/', 'view');
    });

    Route::controller(VisitorController::class)->prefix('visits')
    ->group(function() {
        Route::get('/', 'view');
    });

    Route::controller(GateController::class)->prefix('gate')
    ->group(function() {
        Route::get('/', 'view');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });

    Route::controller(SecurityController::class)->prefix('security')
    ->group(function() {
        Route::get('/', 'view');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });
});