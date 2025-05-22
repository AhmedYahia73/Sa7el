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
use App\Http\Controllers\api\Village\VillageSinglePage\AdminController;
use App\Http\Controllers\api\Village\Posts\PostsController;
use App\Http\Controllers\api\Village\MaintenanceType\MaintenanceTypeController;
use App\Http\Controllers\api\Village\PaymentRequest\PaymentRequestController;
use App\Http\Controllers\api\Village\Home\HomeController;
use App\Http\Controllers\api\Village\ServiceType\ServiceTypeController;

Route::middleware(['auth:sanctum', 'IsVillage', 'can:package_roles'])->group(function(){
    Route::controller(HomeController::class)->prefix('home')->middleware('can:Home')
    ->group(function() {
        Route::get('/', 'view');
    });

    Route::controller(ServiceTypeController::class)->prefix('service_type')
    ->middleware('can:Service Type')->group(function() {
        Route::get('/', 'view'); 
        Route::post('/add', 'add'); 
        Route::delete('/delete', 'delete'); 
    });

    Route::controller(OwnerController::class)->prefix('owner')->middleware('can:owner')
    ->group(function() {
        Route::get('/', 'view'); 
    });

    Route::controller(PaymentRequestController::class)->prefix('payment_request')
    ->middleware('can:Payment')
    ->group(function() {
        Route::get('/', 'view');
        Route::put('/status/{id}', 'status');
    });

    Route::controller(InfoController::class)->prefix('info_village')->middleware('can:Village Page')
    ->group(function() {
        Route::get('/', 'view');
    });

    Route::controller(MaintenanceTypeController::class)->prefix('maintenance_type')->middleware('can:Maintenance Type')
    ->group(function() {
        Route::get('/', 'view'); 
        Route::post('/add', 'add');
        Route::delete('/delete', 'delete');
    });

    Route::controller(ProfileImageVillageController::class)->prefix('profile_image_village')->middleware('can:Village Page')
    ->group(function() {
        Route::get('/', 'view'); 
        Route::post('/add', 'create');
        Route::delete('/delete', 'delete');
    });

    Route::controller(CoverVillageController::class)->prefix('cover_village')->middleware('can:Village Page')
    ->group(function() {
        Route::get('/', 'view'); 
        Route::post('/add', 'create');
        Route::delete('/delete', 'delete');
    });

    Route::controller(RentController::class)->prefix('rents')->middleware('can:Rent')
    ->group(function() {
        Route::get('/', 'view'); 
    });

    Route::controller(ForRentSaleController::class)->prefix('for_rent_sale')->middleware('can:For Rent & Sale')
    ->group(function() {
        Route::get('/', 'view'); 
    });

    Route::controller(MaintenanceFeezController::class)->prefix('maintenance_feez')->middleware('can:Maintenance Fees')
    ->group(function() {
        Route::get('/', 'view');
        Route::get('/maintenanace_users/{id}', 'maintenanace_users');
        Route::get('/year', 'view_year');
        Route::post('/add_payment', 'add_payment');
    });

    Route::controller(GallaryController::class)->prefix('gallery')->middleware('can:Village Page')
    ->group(function() {
        Route::get('/', 'view');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::delete('/delete/{id}', 'delete');
    });

    Route::controller(AdminController::class)->prefix('admin_village')->middleware('can:Village Page')
    ->group(function() {
        Route::get('/', 'view');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });

    Route::controller(AppartmentController::class)->prefix('appartment')->middleware('can:Appartment')
    ->group(function() {
        Route::get('/', 'view');
        Route::post('/create_code', 'create_code');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });

    Route::controller(PostsController::class)->prefix('post')->middleware('can:News Feed')
    ->group(function() {
        Route::get('/', 'view');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });

    Route::controller(PoolController::class)->prefix('pool')->middleware('can:Pool')
    ->group(function() {
        Route::get('/', 'view');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
        Route::get('/view_gallery/{id}', 'view_gallery');
        Route::post('/add_gallery/{id}', 'add_gallery');
        Route::delete('/delete_gallery/{id}', 'delete_gallery');
    });

    Route::controller(BeachController::class)->prefix('beach')->middleware('can:Beach')
    ->group(function() {
        Route::get('/', 'view');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
        Route::get('/view_gallery/{id}', 'view_gallery');
        Route::post('/add_gallery/{id}', 'add_gallery');
        Route::delete('/delete_gallery/{id}', 'delete_gallery');
    });

    Route::controller(ServiceController::class)->prefix('service')->middleware('can:Services')
    ->group(function() {
        Route::get('/', 'view');
    });

    Route::controller(ProblemController::class)->prefix('problem')->middleware('can:Problem Reports')
    ->group(function() {
        Route::get('/', 'view');
        Route::put('/status/{id}', 'status');
    });

    Route::controller(MaintenanceController::class)->prefix('maintenance')->middleware('can:Maintenance Request')
    ->group(function() {
        Route::get('/', 'view');
        Route::put('/status/{id}', 'status');
    });

    Route::controller(VisitorController::class)->prefix('visits')->middleware('can:Visits')
    ->group(function() {
        Route::get('/', 'view');
    });

    Route::controller(GateController::class)->prefix('gate')->middleware('can:Gates')
    ->group(function() {
        Route::get('/', 'view');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });

    Route::controller(SecurityController::class)->prefix('security')->middleware('can:Security Man')
    ->group(function() {
        Route::get('/', 'view');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });
});