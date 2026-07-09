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
use App\Http\Controllers\api\Village\VisitorLimit\VisitorLimitController;
use App\Http\Controllers\api\Village\PaymentPackage\PaymentPackageController;
use App\Http\Controllers\api\Village\Appartments\AppartmentProfileController;
use App\Http\Controllers\api\Village\Notification\NotificationController;
use App\Http\Controllers\api\Village\Entrance\EntranceController;
use App\Http\Controllers\api\Village\LandingPage\LandingPageControlle;
use App\Http\Controllers\api\Village\Requests\RequestController;
use App\Http\Controllers\api\Village\Setting\SettingController;
use App\Http\Controllers\api\Village\notification\PushNotificationController;

use Illuminate\Support\Facades\Broadcast;

Broadcast::routes(['middleware' => ['auth:sanctum']]);
Route::controller(LandingPageControlle::class)->prefix('landing_page')
->group(function() {
    Route::get('/lists', 'lists');  
    Route::get('/village/{id}', 'view');  
});

Route::middleware(['auth:sanctum', 'IsVillage'])->group(function(){
    Route::controller(HomeController::class)->prefix('home')->middleware('can:Home')
    ->group(function() {
        Route::get('/', 'view');
        Route::get('/filter', 'filter');
    });

    Route::controller(PushNotificationController::class)
    ->prefix('push_notification')->group(function() {
        Route::post('/', 'push_notification');
    });

    Route::controller(SettingController::class)->prefix('setting')
    ->group(function() {
        Route::get('/', 'view');
        Route::get('/lists', 'lists');
        Route::get('/{id}', 'show');
        Route::post('/add', 'store');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'delete');
    });

    Route::controller(RequestController::class)->prefix('code_request')
    ->group(function() {
        Route::get('/', 'code_request');  
        Route::put('/status/{id}', 'code_request_status');  
    });

    Route::controller(RequestController::class)->prefix('login_request')
    ->group(function() {
        Route::get('/', 'login_request');  
        Route::put('/status/{id}', 'login_request_status');  
    });

    Route::controller(NotificationController::class)->prefix('notifications')
    ->group(function() {
        Route::get('/', 'notification');  
        Route::get('/items', 'notification_items');  
        Route::post('/is_read', 'is_read');  
        Route::get('/read_all', 'read_all');  
    });

    Route::controller(ServiceTypeController::class)->prefix('service_type')
    ->middleware('can:Service Type')->group(function() {
        Route::get('/', 'view'); 
        Route::post('/add', 'add'); 
        Route::delete('/delete', 'delete'); 
    });

    Route::controller(EntranceController::class)->prefix('entrance')
    ->group(function() {
        Route::get('/gate', 'entrance_gate');
        Route::get('/beach', 'entrance_beach');
        Route::get('/pool', 'entrance_pool');
    });

    Route::controller(PaymentPackageController::class)->prefix('payment_package')
    ->group(function() {
        Route::get('/lists', 'view');
        Route::get('/invoice', 'invoice'); 
        Route::post('/', 'payment');
    });

    Route::controller(OwnerController::class)->prefix('owner')->middleware('can:owner')
    ->group(function() {
        Route::get('/', 'view'); 
        Route::get('/owners', 'owners'); 
        Route::get('/item/{id}', 'owner'); 
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
        Route::get('/online_users', 'online_users');
        Route::get('/logout_user/{id}', 'logout_user');
    });

    Route::controller(MaintenanceTypeController::class)->prefix('maintenance_type')->middleware('can:Maintenance Type')
    ->group(function() {
        Route::get('/', 'view'); 
        Route::put('/status/{id}', 'status');
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
        Route::get('/all', 'renters'); 
        Route::post('/unit_renters', 'unit_renters'); 
        Route::post('/delete_user', 'delete_user'); 
        Route::post('/delete_code', 'delete_code'); 
    });

    Route::controller(ForRentSaleController::class)->prefix('for_rent_sale')->middleware('can:For Rent & Sale')
    ->group(function() {
        Route::get('/', 'view'); 
    });

    Route::controller(MaintenanceFeezController::class)->prefix('maintenance_feez')->middleware('can:Maintenance Fees')
    ->group(function() {
        Route::get('/item/{id}', 'view');
        Route::get('/maintenanace_users/{id}', 'maintenanace_users');
        Route::get('/year', 'view_year');
        Route::post('/add_payment', 'add_payment');
        
        Route::get('/view_maintanence', 'view_maintanence');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });

    Route::controller(GallaryController::class)->prefix('gallery')->middleware('can:Village Page')
    ->group(function() {
        Route::get('/', 'view');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::delete('/delete/{id}', 'delete');
    });

    Route::controller(VisitorLimitController::class)
    ->prefix('visitor_limit')->middleware('can:Settings')
    ->group(function() {
        Route::get('/', 'view'); 
        Route::post('/add', 'create');
        Route::post('/update', 'modify'); 
    });

    Route::controller(AdminController::class)->prefix('admin_village')->middleware('can:Village Page')
    ->group(function() {
        Route::get('/', 'view');
        Route::get('/my_profile', 'my_profile');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });

    Route::controller(AppartmentController::class)->prefix('appartment')->middleware('can:Appartment')
    ->group(function() {
        Route::get('/', 'view');
        Route::get('/appartement_list', 'appartement_list');
        Route::get('/view_codes/{id}', 'view_codes');
        Route::post('/create_code', 'create_code');
        Route::post('/delete_user_appartment', 'delete_user_appartment');
        Route::put('/update_code/{id}', 'update_code');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });

    Route::controller(AppartmentProfileController::class)
    ->prefix('appartment_profile')->middleware('can:Appartment')
    ->group(function() {
        Route::get('/{id}', 'profile_unit'); 
        Route::put('/update/{id}', 'update_user_type'); 
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