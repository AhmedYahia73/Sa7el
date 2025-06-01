<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\api\SuperAdmin\zones\ZoneController;
use App\Http\Controllers\api\SuperAdmin\village\VillageController;
use App\Http\Controllers\api\SuperAdmin\village\VillageGallaryController;
use App\Http\Controllers\api\SuperAdmin\village\VillageAdminController;
use App\Http\Controllers\api\SuperAdmin\village\VillageRolesController;
use App\Http\Controllers\api\SuperAdmin\appartment_type\AppartmentTypeController;
use App\Http\Controllers\api\SuperAdmin\service_type\ServiceTypeController;
use App\Http\Controllers\api\SuperAdmin\users\UserController;
use App\Http\Controllers\api\SuperAdmin\Provider\ProviderController;
use App\Http\Controllers\api\SuperAdmin\Provider\ProviderAdminController;
use App\Http\Controllers\api\SuperAdmin\Provider\ProviderGalleryController;
use App\Http\Controllers\api\SuperAdmin\Provider\ProviderRolesController;
use App\Http\Controllers\api\SuperAdmin\payment_method\PaymentMethodController;
use App\Http\Controllers\api\SuperAdmin\subscription\SubscriptionController;
use App\Http\Controllers\api\SuperAdmin\subscriper\SubscriperController;
use App\Http\Controllers\api\SuperAdmin\payment\PaymentController;
use App\Http\Controllers\api\SuperAdmin\Admin\AdminController;
use App\Http\Controllers\api\SuperAdmin\invoice\InvoiceController;
use App\Http\Controllers\api\SuperAdmin\Provider\ProviderCoverController;
use App\Http\Controllers\api\SuperAdmin\village\VillageCoverController;
use App\Http\Controllers\api\SuperAdmin\MaintenanceType\MaintenanceTypeController;
use App\Http\Controllers\api\SuperAdmin\ServiceProvider\ServiceProviderController;
use App\Http\Controllers\api\SuperAdmin\Mall\MallController;
use App\Http\Controllers\api\SuperAdmin\Mall\MallGallaryController;
use App\Http\Controllers\api\SuperAdmin\Mall\MallCoverController;

Route::middleware(['auth:sanctum', 'IsAdmin'])->group(function(){
    Route::controller(ZoneController::class)->prefix('zone')
    ->middleware('can:Admin_Admin')->group(function() {
        Route::get('/', 'view');
        Route::get('/item/{id}', 'zone');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });

    Route::controller(MallController::class)->prefix('mall')
    ->group(function() {
        Route::get('/', 'view');
        Route::post('/update_profile_image/{id}', 'update_profile_image');
        Route::get('/item/{id}', 'mall');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
        Route::get('/providers', 'provider_mall');
    });

    Route::controller(MallGallaryController::class)->prefix('mall_gallery')
    ->group(function() {
        Route::get('/{id}', 'view'); 
        Route::put('/status/{id}', 'status');
        Route::post('/add/{id}', 'create');
        Route::delete('/delete/{id}', 'delete');
    });

    Route::controller(MallCoverController::class)->prefix('mall_cover')
    ->group(function() {
        Route::get('/{id}', 'view');
        Route::post('/add/{id}', 'create');
        Route::delete('/delete/{id}', 'delete');
    });

    
    Route::controller(ServiceProviderController::class)->prefix('service_provider')
    ->group(function() {
        Route::get('/', 'view');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });

    Route::controller(MaintenanceTypeController::class)->prefix('maintenance_type')
    ->middleware('can:Admin_Admin')->group(function() {
        Route::get('/', 'view'); 
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });
    
    Route::controller(ProviderCoverController::class)->prefix('provider_cover')
    ->group(function() {
        Route::get('/{id}', 'view'); 
        Route::post('/add/{id}', 'create');
        Route::delete('/delete/{id}', 'delete');
    });
    
    Route::controller(VillageCoverController::class)->prefix('village_cover')
    ->middleware('can:Admin_Admin')->group(function() {
        Route::get('/{id}', 'view'); 
        Route::post('/add/{id}', 'create');
        Route::delete('/delete/{id}', 'delete');
    });
    
    Route::controller(AdminController::class)->prefix('admins')
    ->middleware('can:Admin_Admin')->group(function() {
        Route::get('/', 'view'); 
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });
    
    Route::controller(VillageController::class)->prefix('village')
    ->middleware('can:Admin_Admin')->group(function() {
        Route::get('/', 'view');
        Route::get('/item/{id}', 'village');
        Route::put('/status/{id}', 'status');
        Route::post('/update_profile_image/{id}', 'update_profile_image');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');

        Route::post('/village_units', 'village_units');
        Route::post('/village_units_delete', 'village_units_delete');
    });
    
    Route::controller(VillageGallaryController::class)->prefix('village_gallery')
    ->middleware('can:Admin_Admin')->group(function() {
        Route::get('/{id}', 'view');
        Route::put('/status/{id}', 'status');
        Route::post('/add/{id}', 'create'); 
        Route::delete('/delete/{id}', 'delete');
    });
    
    Route::controller(VillageAdminController::class)->prefix('village_admin')
    ->middleware('can:Admin_Admin')->group(function() {
        Route::get('/{id}', 'view');
        Route::get('/item/{id}', 'admin');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });
    
    Route::controller(VillageRolesController::class)->prefix('village_roles')
    ->middleware('can:Admin_Admin')->group(function() {
        Route::get('/', 'view');
        Route::get('/item/{id}', 'position');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });
    
    Route::controller(AppartmentTypeController::class)->prefix('appartment_type')
    ->middleware('can:Admin_Admin')->group(function() {
        Route::get('/', 'view');
        Route::get('/item/{id}', 'appartment_type');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });
    
    Route::controller(UserController::class)->prefix('user')
    ->middleware('can:Admin_Admin')->group(function() {
        Route::get('/', 'view');
        Route::get('/item/{id}', 'user');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });
    
    Route::controller(ServiceTypeController::class)->prefix('service_type')
    ->group(function() {
        Route::get('/', 'view');
        Route::get('/item/{id}', 'service_type');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });
    
    Route::controller(ProviderController::class)->prefix('provider')
    ->group(function() {
        Route::get('/', 'view');
        Route::get('/item/{id}', 'provider');
        Route::put('/status/{id}', 'status');
        Route::post('/update_profile_image/{id}', 'update_profile_image');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });
    
    Route::controller(ProviderGalleryController::class)->prefix('provider_gallary')
    ->group(function() {
        Route::get('/{id}', 'view');
        Route::put('/status/{id}', 'status');
        Route::post('/add/{id}', 'create');
        Route::delete('/delete/{id}', 'delete');
    });
    
    Route::controller(ProviderAdminController::class)->prefix('provider_admin')
    ->group(function() {
        Route::get('/{id}', 'view');
        Route::get('/item/{id}', 'admin');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });
    
    Route::controller(ProviderRolesController::class)->prefix('provider_roles')
    ->group(function() {
        Route::get('/', 'view');
        Route::get('/item/{id}', 'position');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });
    
    Route::controller(PaymentMethodController::class)->prefix('payment_method')
    ->middleware('can:Admin_Admin')->group(function() {
        Route::get('/', 'view');
        Route::get('/item/{id}', 'payment_method');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });
    
    Route::controller(PaymentMethodController::class)->prefix('payment_method')
    ->middleware('can:Admin_Admin')->group(function() {
        Route::get('/', 'view');
        Route::get('/item/{id}', 'payment_method');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });
    
    Route::controller(SubscriptionController::class)->prefix('subscription')
    ->middleware('can:Admin_Admin')->group(function() {
        Route::get('/', 'view');
        Route::get('/item/{id}', 'package');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });
    
    Route::controller(SubscriperController::class)->prefix('subscriper')
    ->middleware('can:Admin_Admin')->group(function() {
        Route::get('/', 'view'); 
        Route::get('/item/{id}', 'filter');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });
    
    Route::controller(PaymentController::class)->prefix('payments')
    ->middleware('can:Admin_Admin')->group(function() {
        Route::get('/', 'view');
        Route::put('/approve/{id}', 'approve');
        Route::put('/reject/{id}', 'reject');
    });
    
    Route::controller(InvoiceController::class)->prefix('invoice')
    ->middleware('can:Admin_Admin')->group(function() {
        Route::get('/{id}', 'invoice');
    });
});