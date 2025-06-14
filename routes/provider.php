<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\api\ServiceProvider\Contact\ContactController;
use App\Http\Controllers\api\ServiceProvider\Menue\MenueController;

Route::middleware(['auth:sanctum', 'IsProvider'])->group(function(){
    Route::controller(ContactController::class)->prefix('contact')
    ->group(function() {
        Route::get('/', 'view'); 
        Route::post('/update', 'update');
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
});