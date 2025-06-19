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
use App\Http\Controllers\api\User\MaintenanceFeez\MaintenanceFeezController;
use App\Http\Controllers\api\User\Visitors\MyVisitorsController;
use App\Http\Controllers\api\User\Entrance\EntranceController;
use App\Http\Controllers\api\User\Profile\ProfileController;
use App\Http\Controllers\api\User\MaintenanceProvider\MaintenanceProviderController;

Route::middleware(['auth:sanctum', 'IsUser'])->group(function(){
    Route::controller(PropertyController::class)->prefix('property')
    ->group(function() {
        Route::get('/', 'my_property'); 
        Route::post('/add', 'add_property'); 
    });

    Route::controller(MaintenanceProviderController::class)->prefix('maintenance_provider')
    ->group(function() {
        Route::get('/', 'view');
        Route::put('/love/{id}', 'love');
        Route::get('/love_history', 'love_history');
        Route::put('/image_love/{id}', 'image_love');
        Route::put('/video_love/{id}', 'video_love');
    });

    Route::controller(EntranceController::class)->prefix('entranance')
    ->group(function() {
        Route::get('/', 'view');  
    });

    Route::controller(MyVisitorsController::class)->prefix('my_visitors')
    ->group(function() {
        Route::get('/', 'view');  
    });

    Route::controller(VisitController::class)->prefix('visitor')
    ->group(function() {
        Route::get('/create_qr_code', 'create_qr_code'); 
        Route::get('/create_code', 'create_code');
        Route::post('/visitor_qr', 'visitor_qr');
    });

    Route::controller(MaintenanceFeezController::class)->prefix('maintenance_feez')
    ->group(function() {
        Route::get('/', 'view');
        Route::get('/view_year', 'view_year'); 
        Route::post('/payment_request', 'make_payment_request'); 
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
        Route::get('provider/love_history', 'love_history');
        Route::put('provider/love/{id}', 'love');
        Route::put('provider_image/love/{id}', 'image_love');
        Route::put('provider_video/love/{id}', 'video_love');
    });

    Route::controller(RentController::class)
    ->group(function() {
        Route::get('/rent', 'view');
        Route::post('/rent/add', 'create');
    });

    Route::controller(OfferController::class)->prefix('offer')
    ->group(function() {
        Route::get('/', 'view');
        Route::post('/offer_status/{id}', 'offer_status');
        Route::get('/appartment_offer', 'appartment_offer');
        Route::get('/appartment_image', 'appartment_image');
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

    Route::controller(ProfileController::class)->prefix('profile')
    ->group(function() {
        Route::get('/', 'profile');
        Route::post('/update_profile', 'update_profile');
    });
});