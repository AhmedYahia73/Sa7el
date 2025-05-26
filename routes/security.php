<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route; 

use App\Http\Controllers\api\Security\Home\HomeController;
use App\Http\Controllers\api\Security\Gate\GateController;
use App\Http\Controllers\api\Security\Pool\PoolController;
use App\Http\Controllers\api\Security\Beach\BeachController;
use App\Http\Controllers\api\Security\Profile\ProfileController;

Route::middleware(['auth:sanctum', 'IsSecurity'])->group(function(){
    Route::controller(HomeController::class)->prefix('home')
    ->group(function() {
        Route::get('/', 'view');
        Route::get('/entrance_gate', 'entrance_gate');
        Route::get('/entrance_pool', 'entrance_pool');
        Route::get('/entrance_beach', 'entrance_beach');
    });

    Route::controller(ProfileController::class)->prefix('profile')
    ->group(function() {
        Route::get('/', 'profile');
        Route::post('/update', 'update_profile');
    });

    Route::controller(GateController::class)->prefix('gate_qr')
    ->group(function() {
        Route::post('/', 'read_qr');
        Route::post('/upload_id', 'upload_id');
    });

    Route::controller(PoolController::class)->prefix('pool_qr')
    ->group(function() {
        Route::post('/', 'read_qr');
    });

    Route::controller(BeachController::class)->prefix('beach_qr')
    ->group(function() {
        Route::post('/', 'read_qr');
    });
});