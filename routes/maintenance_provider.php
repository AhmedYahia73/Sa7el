<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\api\MaintenanceProvider\Contact\ContactController;
use App\Http\Controllers\api\MaintenanceProvider\ServicePrice\ServicePriceController;
use App\Http\Controllers\api\MaintenanceProvider\Gallery\GalleryController;
use App\Http\Controllers\api\MaintenanceProvider\Video\VideoController;
use App\Http\Controllers\api\MaintenanceProvider\Invoice\InvoicesController;
use App\Http\Controllers\api\MaintenanceProvider\Offer\OfferController;
use App\Http\Controllers\api\User\Profile\ProfileController;

Route::middleware(['auth:sanctum', 'IsMaintenanceProvider'])->group(function(){
    Route::controller(ContactController::class)->prefix('contact')
    ->group(function() {
        Route::get('/', 'view'); 
        Route::post('/update', 'update');
    });
        
    Route::controller(InvoicesController::class)->prefix('invoice')
    ->group(function() {
        Route::get('/', 'view');
    });
    
    Route::controller(ServicePriceController::class)->prefix('service_price')
    ->group(function() {
        Route::get('/', 'view'); 
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::delete('/delete/{id}', 'delete');
    });
    
    Route::controller(GalleryController::class)->prefix('gallery')
    ->group(function() {
        Route::get('/', 'view'); 
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::delete('/delete/{id}', 'delete');
    });
    
    Route::controller(VideoController::class)->prefix('videos')
    ->group(function() {
        Route::get('/', 'view'); 
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });
    
    Route::controller(OfferController::class)->prefix('offer')
    ->group(function() {
        Route::get('/', 'view'); 
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });

    Route::controller(ProfileController::class)->prefix('profile')
    ->group(function() {
        Route::get('/', 'profile');
        Route::post('/update_profile', 'update_profile');
    });
});