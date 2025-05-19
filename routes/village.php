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
use App\Http\Controllers\api\Village\ForRentSale\ForRentSaleController;
use App\Http\Controllers\api\Village\Rent\RentController;
use App\Http\Controllers\api\Village\Gallary\GallaryController;
use App\Http\Controllers\api\Village\VillageSinglePage\ProfileImageVillageController;
use App\Http\Controllers\api\Village\VillageSinglePage\CoverVillageController;
use App\Http\Controllers\api\Village\VillageSinglePage\InfoController;

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

    Route::controller(InfoController::class)->prefix('info_village')
    ->group(function() {
        Route::get('/', 'view');
    });

    Route::controller(ProfileImageVillageController::class)->prefix('profile_image_village')
    ->group(function() {
        Route::get('/', 'view'); 
        Route::post('/add', 'create');
        Route::delete('/delete', 'delete');
    });

    Route::controller(CoverVillageController::class)->prefix('cover_village')
    ->group(function() {
        Route::get('/', 'view'); 
        Route::post('/add', 'create');
        Route::delete('/delete', 'delete');
    });

    Route::controller(RentController::class)->prefix('rents')
    ->group(function() {
        Route::get('/', 'view'); 
    });

    Route::controller(ForRentSaleController::class)->prefix('for_rent_sale')
    ->group(function() {
        Route::get('/', 'view'); 
    });

    Route::controller(MaintenanceFeezController::class)->prefix('maintenance_feez')
    ->group(function() {
        Route::get('/', 'view');
        Route::get('/year', 'view_year');
        Route::post('/add_payment', 'add_payment');
    });

    Route::controller(GallaryController::class)->prefix('gallery')
    ->group(function() {
        Route::get('/', 'view');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::delete('/delete/{id}', 'delete');
    });

    Route::controller(AppartmentController::class)->prefix('appartment')
    ->group(function() {
        Route::get('/', 'view');
        Route::post('/create_code', 'create_code');
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