<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route; 

use App\Http\Controllers\api\Security\Home\HomeController;
use App\Http\Controllers\api\Security\Gate\GateController;

Route::middleware(['auth:sanctum', 'IsSecurity'])->group(function(){
    Route::controller(HomeController::class)->prefix('home')
    ->group(function() {
        Route::get('/', 'view');
        Route::get('/entrance_gate', 'entrance_gate');
        Route::get('/entrance_pool', 'entrance_pool');
        Route::get('/entrance_beach', 'entrance_beach');
    });

    Route::controller(GateController::class)->prefix('gate_qr')
    ->group(function() {
        Route::post('/', 'read_qr');
    });
});