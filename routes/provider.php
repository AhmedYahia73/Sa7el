<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\api\ServiceProvider\Contact\ContactController;
use App\Http\Controllers\api\ServiceProvider\Menue\MenueController;
use App\Http\Controllers\api\ServiceProvider\Gallery\GalleryController;
use App\Http\Controllers\api\ServiceProvider\Video\VideoController;
use App\Http\Controllers\api\ServiceProvider\Invoice\InvoicesController;
use App\Http\Controllers\api\ServiceProvider\Offer\OfferController;
use App\Http\Controllers\api\ServiceProvider\WorkHours\WorkHoursController;
use App\Http\Controllers\api\User\Profile\ProfileController;

Route::middleware(['auth:sanctum', 'IsProvider'])->group(function(){
    Route::controller(ContactController::class)->prefix('contact')
    ->group(function() {
        Route::get('/', 'view'); 
        Route::post('/update', 'update');
    });
        
    Route::controller(InvoicesController::class)->prefix('invoice')
    ->group(function() {
        Route::get('/', 'view');
    });
    
    Route::controller(MenueController::class)->prefix('menue')
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

    Route::controller(WorkHoursController::class)->prefix('work_hours')
    ->group(function() {
        Route::get('/', 'view');
        Route::post('/update', 'update');
    });
});