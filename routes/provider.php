<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\api\ServiceProvider\Contact\ContactController;

Route::middleware(['auth:sanctum', 'IsProvider'])->group(function(){
    Route::controller(ContactController::class)->prefix('contact')
    ->group(function() {
        Route::get('/', 'view'); 
        Route::post('/update', 'update');
    });
});