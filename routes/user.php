<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\api\User\Property\PropertyController;


Route::middleware(['auth:sanctum', 'IsUser'])->group(function(){
    Route::controller(PropertyController::class)->prefix('property')
    ->group(function() {
        Route::get('/', 'my_property'); 
        Route::post('/add', 'add_property'); 
    });
});