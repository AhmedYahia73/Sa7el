<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\api\User\Property\PropertyController;
use App\Http\Controllers\api\User\Visit\VisitController;
use App\Http\Controllers\api\User\Maintenance\MaintenanceController;
use App\Http\Controllers\api\User\ProblemReport\ProblemReportController;
use App\Http\Controllers\api\User\PoolBeaches\PoolBeachesController;
use App\Http\Controllers\api\User\Services\ServiceController;
use App\Http\Controllers\api\User\rent\RentController;
use App\Http\Controllers\api\User\Offers\OfferController;
use App\Http\Controllers\api\User\Posts\PostsController;

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
        Route::get('/history', 'history');
        Route::post('/add', 'maintenance_request');
    });

    Route::controller(ProblemReportController::class)->prefix('problem_report')
    ->group(function() {
        Route::post('/add', 'add_report'); 
        Route::get('/history', 'history');
    });

    Route::controller(PoolBeachesController::class)
    ->group(function() {
        Route::get('/pools', 'pools'); 
        Route::get('/beaches', 'beaches'); 
    });

    Route::controller(ServiceController::class)
    ->group(function() {
        Route::get('/services', 'view');
        Route::get('/out_service', 'out_service');
    });

    Route::controller(RentController::class)
    ->group(function() {
        Route::get('/rent', 'view');
        Route::post('/rent/add', 'create');
    });

    Route::controller(OfferController::class)->prefix('offer')
    ->group(function() {
        Route::get('/', 'view');
        Route::put('/offer_status/{id}', 'offer_status');
        Route::get('/appartment_image', 'appartment_image');
        Route::get('/appartment', 'appartment');
        Route::post('/upload_appartment_image', 'upload_appartment_image');
        Route::post('/add_rent', 'add_rent');
        Route::post('/add_sale', 'add_sale');
        Route::post('/update_rent/{id}', 'update_rent');
        Route::post('/update_sale/{id}', 'update_sale');
        Route::delete('/delete/{id}', 'delete');
    });

    Route::controller(PostsController::class)->prefix('post')
    ->group(function() {
        Route::get('/', 'view');
        Route::post('/react', 'react');
    });
});