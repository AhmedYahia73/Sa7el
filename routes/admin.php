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
use App\Http\Controllers\api\SuperAdmin\Admin\AdminRoleController;
use App\Http\Controllers\api\SuperAdmin\invoice\InvoiceController;
use App\Http\Controllers\api\SuperAdmin\Provider\ProviderCoverController;
use App\Http\Controllers\api\SuperAdmin\village\VillageCoverController;
use App\Http\Controllers\api\SuperAdmin\MaintenanceType\MaintenanceTypeController;
use App\Http\Controllers\api\SuperAdmin\Mall\MallController;
use App\Http\Controllers\api\SuperAdmin\Mall\MallGallaryController;
use App\Http\Controllers\api\SuperAdmin\Mall\MallCoverController;
use App\Http\Controllers\api\SuperAdmin\Home\HomeController;
use App\Http\Controllers\api\SuperAdmin\ServiceProvider\ServiceProviderController;
use App\Http\Controllers\api\SuperAdmin\ServiceProvider\MaintenanceProviderAdminController;
use App\Http\Controllers\api\SuperAdmin\ServiceProvider\MaintenanceProviderCoverController;
use App\Http\Controllers\api\SuperAdmin\ServiceProvider\MaintenanceProviderGalleryController;
use App\Http\Controllers\api\SuperAdmin\ServiceProvider\MaintenanceProviderRolesController;
use App\Http\Controllers\api\User\Profile\ProfileController;

Route::middleware(['auth:sanctum', 'IsAdmin'])->group(function(){
    Route::controller(HomeController::class)->prefix('home')
    ->group(function() {
        Route::get('/', 'view')->middleware('can:admin_Home_view'); 
    });

    Route::controller(ZoneController::class)->prefix('zone')
    ->group(function() {
        Route::get('/', 'view')->middleware('can:admin_zone_view');
        Route::get('/item/{id}', 'zone')->middleware('can:admin_zone_view');
        Route::put('/status/{id}', 'status')->middleware('can:admin_zone_status');
        Route::post('/add', 'create')->middleware('can:admin_zone_add');
        Route::post('/update/{id}', 'modify')->middleware('can:admin_zone_edit');
        Route::delete('/delete/{id}', 'delete')->middleware('can:admin_zone_delete');
    });

    Route::controller(MallController::class)->prefix('mall')
    ->group(function() {
        Route::get('/', 'view')->middleware('can:admin_Mall_view');
        Route::post('/update_profile_image/{id}', 'update_profile_image')->middleware('can:admin_zone_delete');
        Route::get('/item/{id}', 'mall')->middleware('can:admin_Mall_view');
        Route::put('/status/{id}', 'status')->middleware('can:admin_Mall_status');
        Route::post('/add', 'create')->middleware('can:admin_Mall_add');
        Route::post('/update/{id}', 'modify')->middleware('can:admin_Mall_edit');
        Route::delete('/delete/{id}', 'delete')->middleware('can:admin_Mall_delete');
        Route::get('/providers', 'provider_mall')->middleware('can:update_profile');
    });

    Route::controller(MallGallaryController::class)->prefix('mall_gallery')
    ->group(function() {
        Route::get('/{id}', 'view')->middleware('can:admin_mall_gallery_view');
        Route::put('/status/{id}', 'status')->middleware('can:admin_mall_gallery_status');
        Route::post('/add/{id}', 'create')->middleware('can:admin_mall_gallery_add');
        Route::delete('/delete/{id}', 'delete')->middleware('can:admin_mall_gallery_delete');
    });

    Route::controller(MallCoverController::class)->prefix('mall_cover')
    ->group(function() {
        Route::get('/{id}', 'view')->middleware('can:admin_mall_cover_view');
        Route::post('/add/{id}', 'create')->middleware('can:admin_mall_cover_add');
        Route::delete('/delete/{id}', 'delete')->middleware('can:admin_mall_cover_delete');
    });
    
    Route::controller(ServiceProviderController::class)->prefix('service_provider')
    ->group(function() {
        Route::get('/', 'view')->middleware('can:admin_provider_maintenance_view');
        Route::put('/status/{id}', 'status')->middleware('can:admin_provider_maintenance_status');
        Route::post('/add', 'create')->middleware('can:admin_provider_maintenance_add');
        Route::post('/update/{id}', 'modify')->middleware('can:admin_provider_maintenance_edit');
        Route::delete('/delete/{id}', 'delete')->middleware('can:admin_provider_maintenance_delete');
    });
    
    Route::controller(MaintenanceProviderAdminController::class)->prefix('maintenance_provider_admin')
    ->group(function() {
        Route::get('/{id}', 'view')->middleware('can:admin_provider_maintenance_admin_view');
        Route::get('/item/{id}', 'admin')->middleware('can:admin_provider_maintenance_admin_view');
        Route::put('/status/{id}', 'status')->middleware('can:admin_provider_maintenance_admin_status');
        Route::post('/add', 'create')->middleware('can:admin_provider_maintenance_admin_add');
        Route::post('/update/{id}', 'modify')->middleware('can:admin_provider_maintenance_admin_edit');
        Route::delete('/delete/{id}', 'delete')->middleware('can:admin_provider_maintenance_admin_delete');
    });
    
    Route::controller(MaintenanceProviderCoverController::class)->prefix('maintenance_provider_cover')
    ->group(function() {
        Route::get('/{id}', 'view')->middleware('can:admin_provider_maintenance_cover_view');
        Route::post('/add/{id}', 'create')->middleware('can:admin_provider_maintenance_cover_add');
        Route::delete('/delete/{id}', 'delete')->middleware('can:admin_provider_maintenance_cover_delete');
    });
    
    Route::controller(MaintenanceProviderGalleryController::class)->prefix('maintenance_provider_gallary')
    ->group(function() {
        Route::get('/{id}', 'view')->middleware('can:admin_provider_maintenance_gallery_view');
        Route::put('/status/{id}', 'status')->middleware('can:admin_provider_maintenance_gallery_status');
        Route::post('/add/{id}', 'create')->middleware('can:admin_provider_maintenance_gallery_add');
        Route::delete('/delete/{id}', 'delete')->middleware('can:admin_provider_maintenance_gallery_delete');
    });
    
    Route::controller(MaintenanceProviderRolesController::class)->prefix('maintenance_provider_roles')
    ->group(function() {
        Route::get('/', 'view')->middleware('can:admin_provider_maintenance_admin_role_view');
        Route::get('/item/{id}', 'position')->middleware('can:admin_provider_maintenance_admin_role_view');
        Route::put('/status/{id}', 'status')->middleware('can:admin_provider_maintenance_admin_role_status');
        Route::post('/add', 'create')->middleware('can:admin_provider_maintenance_admin_role_add');
        Route::post('/update/{id}', 'modify')->middleware('can:admin_provider_maintenance_admin_role_edit');
        Route::delete('/delete/{id}', 'delete')->middleware('can:admin_provider_maintenance_admin_role_delete');
    });

    Route::controller(MaintenanceTypeController::class)->prefix('maintenance_type')
    ->group(function() {
        Route::get('/', 'view')->middleware('can:admin_maintenance_type_view'); 
        Route::put('/status/{id}', 'status')->middleware('can:admin_maintenance_type_status');
        Route::post('/add', 'create')->middleware('can:admin_maintenance_type_add');
        Route::post('/update/{id}', 'modify')->middleware('can:admin_maintenance_type_edit');
        Route::delete('/delete/{id}', 'delete')->middleware('can:admin_maintenance_type_delete');
    });
    
    Route::controller(ProviderCoverController::class)->prefix('provider_cover')
    ->group(function() {
        Route::get('/{id}', 'view')->middleware('can:admin_provider_cover_view');
        Route::post('/add/{id}', 'create')->middleware('can:admin_provider_cover_add');
        Route::delete('/delete/{id}', 'delete')->middleware('can:admin_provider_cover_delete');
    });
    
    Route::controller(VillageCoverController::class)->prefix('village_cover')
    ->group(function() {
        Route::get('/{id}', 'view')->middleware('can:admin_village_cover_view');
        Route::post('/add/{id}', 'create')->middleware('can:admin_village_cover_add');
        Route::delete('/delete/{id}', 'delete')->middleware('can:admin_village_cover_delete');
    });
    
    Route::controller(AdminRoleController::class)->prefix('admin_role')
    ->group(function() {
        Route::get('/', 'view')->middleware('can:admin_admin_role_view');
        Route::put('/status/{id}', 'status')->middleware('can:admin_admin_role_status');
        Route::post('/add', 'create')->middleware('can:admin_admin_role_add');
        Route::post('/update/{id}', 'modify')->middleware('can:admin_admin_role_edit');
        Route::delete('/delete/{id}', 'delete')->middleware('can:admin_admin_role_delete');
    });
    
    Route::controller(AdminController::class)->prefix('admins')
    ->group(function() {
        Route::get('/', 'view')->middleware('can:admin_Admin_view');
        Route::put('/status/{id}', 'status')->middleware('can:admin_Admin_status');
        Route::post('/add', 'create')->middleware('can:admin_Admin_add');
        Route::post('/update/{id}', 'modify')->middleware('can:admin_Admin_edit');
        Route::delete('/delete/{id}', 'delete')->middleware('can:admin_Admin_delete');
    });
    
    Route::controller(VillageController::class)->prefix('village')
    ->group(function() {
        Route::get('/', 'view')->middleware('can:admin_Village_view');
        Route::get('/item/{id}', 'village')->middleware('can:admin_Village_view');
        Route::put('/status/{id}', 'status')->middleware('can:admin_Village_status');
        Route::post('/update_profile_image/{id}', 'update_profile_image')->middleware('can:admin_Village_update_profile');
        Route::post('/add', 'create')->middleware('can:admin_Village_add');
        Route::post('/update/{id}', 'modify')->middleware('can:admin_Village_edit');
        Route::delete('/delete/{id}', 'delete')->middleware('can:admin_Village_delete');

        Route::post('/village_units', 'village_units')->middleware('can:admin_Village_view_units');
        Route::post('/village_units_delete', 'village_units_delete')->middleware('can:admin_Village_delete_unit');
    });
    
    Route::controller(VillageGallaryController::class)->prefix('village_gallery')
    ->group(function() {
        Route::get('/{id}', 'view')->middleware('can:admin_village_gallery_view');
        Route::put('/status/{id}', 'status')->middleware('can:admin_village_gallery_status');
        Route::post('/add/{id}', 'create')->middleware('can:admin_village_gallery_add'); 
        Route::delete('/delete/{id}', 'delete')->middleware('can:admin_village_gallery_delete');
    });
    
    Route::controller(VillageAdminController::class)->prefix('village_admin')
    ->group(function() {
        Route::get('/{id}', 'view')->middleware('can:admin_village_admin_view');
        Route::get('/item/{id}', 'admin')->middleware('can:admin_village_admin_view');
        Route::put('/status/{id}', 'status')->middleware('can:admin_village_admin_status');
        Route::post('/add', 'create')->middleware('can:admin_village_admin_add');
        Route::post('/update/{id}', 'modify')->middleware('can:admin_village_admin_edit');
        Route::delete('/delete/{id}', 'delete')->middleware('can:admin_village_admin_delete');
    });
    
    Route::controller(VillageRolesController::class)->prefix('village_roles')
    ->group(function() {
        Route::get('/', 'view')->middleware('can:admin_village_admin_role_view');
        Route::get('/item/{id}', 'position')->middleware('can:admin_viladmin_village_admin_role_viewlage_admin_delete');
        Route::put('/status/{id}', 'status')->middleware('can:admin_village_admin_role_status');
        Route::post('/add', 'create')->middleware('can:admin_village_admin_role_add');
        Route::post('/update/{id}', 'modify')->middleware('can:admin_village_admin_role_edit');
        Route::delete('/delete/{id}', 'delete')->middleware('can:admin_village_admin_role_delete');
    });
    
    Route::controller(AppartmentTypeController::class)->prefix('appartment_type')
    ->group(function() {
        Route::get('/', 'view')->middleware('can:admin_appartment_type_view');
        Route::get('/item/{id}', 'appartment_type')->middleware('can:admin_appartment_type_view');
        Route::put('/status/{id}', 'status')->middleware('can:admin_appartment_type_status');
        Route::post('/add', 'create')->middleware('can:admin_appartment_type_add');
        Route::post('/update/{id}', 'modify')->middleware('can:admin_appartment_type_edit');
        Route::delete('/delete/{id}', 'delete')->middleware('can:admin_appartment_type_delete');
    });
    
    Route::controller(UserController::class)->prefix('user')
    ->group(function() {
        Route::get('/', 'view')->middleware('can:admin_user_view');
        Route::get('/item/{id}', 'user')->middleware('can:admin_user_view');
        Route::put('/status/{id}', 'status')->middleware('can:admin_user_status');
        Route::post('/add', 'create')->middleware('can:admin_user_add');
        Route::post('/update/{id}', 'modify')->middleware('can:admin_user_edit');
        Route::delete('/delete/{id}', 'delete')->middleware('can:admin_user_delete');
    });
    
    Route::controller(ServiceTypeController::class)->prefix('service_type')
    ->group(function() {
        Route::get('/', 'view')->middleware('can:admin_service_type_view');
        Route::get('/item/{id}', 'service_type')->middleware('can:admin_service_type_view');
        Route::put('/status/{id}', 'status')->middleware('can:admin_service_type_status');
        Route::post('/add', 'create')->middleware('can:admin_service_type_add');
        Route::post('/update/{id}', 'modify')->middleware('can:admin_service_type_edit');
        Route::delete('/delete/{id}', 'delete')->middleware('can:admin_service_type_delete');
    });
    
    Route::controller(ProviderController::class)->prefix('provider')
    ->group(function() {
        Route::get('/', 'view')->middleware('can:Admin_Provider_View');
        Route::get('/item/{id}', 'provider')->middleware('can:Admin_Provider_View');
        Route::put('/status/{id}', 'status')->middleware('can:Admin_Provider_Status');
        Route::post('/update_profile_image/{id}', 'update_profile_image')->middleware('can:Admin_Provider_Update');
        Route::post('/add', 'create')->middleware('can:Admin_Provider_Add');
        Route::post('/update/{id}', 'modify')->middleware('can:Admin_Provider_Update');
        Route::delete('/delete/{id}', 'delete')->middleware('can:Admin_Provider_Delete');
    });
    
    Route::controller(ProviderGalleryController::class)->prefix('provider_gallary')
    ->group(function() {
        Route::get('/{id}', 'view')->middleware('can:admin_provider_gallery_view');
        Route::put('/status/{id}', 'status')->middleware('can:admin_provider_gallery_status');
        Route::post('/add/{id}', 'create')->middleware('can:admin_provider_gallery_add');
        Route::delete('/delete/{id}', 'delete')->middleware('can:admin_provider_gallery_delete');
    });
    
    Route::controller(ProviderAdminController::class)->prefix('provider_admin')
    ->group(function() {
        Route::get('/{id}', 'view')->middleware('can:admin_provider_admin_view');
        Route::get('/item/{id}', 'admin')->middleware('can:admin_provider_admin_view');
        Route::put('/status/{id}', 'status')->middleware('can:admin_provider_admin_status');
        Route::post('/add', 'create')->middleware('can:admin_provider_admin_add');
        Route::post('/update/{id}', 'modify')->middleware('can:admin_provider_admin_edit');
        Route::delete('/delete/{id}', 'delete')->middleware('can:admin_provider_admin_delete');
    });
    
    Route::controller(ProviderRolesController::class)->prefix('provider_roles')
    ->group(function() {
        Route::get('/', 'view')->middleware('can:admin_provider_admin_role_view');
        Route::get('/item/{id}', 'position')->middleware('can:admin_provider_admin_role_view');
        Route::put('/status/{id}', 'status')->middleware('can:admin_provider_admin_role_status');
        Route::post('/add', 'create')->middleware('can:admin_provider_admin_role_add');
        Route::post('/update/{id}', 'modify')->middleware('can:admin_provider_admin_role_edit');
        Route::delete('/delete/{id}', 'delete')->middleware('can:admin_provider_admin_role_delete');
    });
    
    Route::controller(PaymentMethodController::class)->prefix('payment_method')
    ->group(function() {
        Route::get('/', 'view')->middleware('can:admin_payment_method_view');
        Route::get('/item/{id}', 'payment_method')->middleware('can:admin_payment_method_view');
        Route::put('/status/{id}', 'status')->middleware('can:admin_payment_method_status');
        Route::post('/add', 'create')->middleware('can:admin_payment_method_add');
        Route::post('/update/{id}', 'modify')->middleware('can:admin_payment_method_edit');
        Route::delete('/delete/{id}', 'delete')->middleware('can:admin_payment_method_delete');
    });
    
    Route::controller(SubscriptionController::class)->prefix('subscription')
    ->group(function() {
        Route::get('/', 'view')->middleware('can:admin_subscription_view');
        Route::get('/item/{id}', 'package')->middleware('can:admin_subscription_view');
        Route::put('/status/{id}', 'status')->middleware('can:admin_subscription_status');
        Route::post('/add', 'create')->middleware('can:admin_subscription_add');
        Route::post('/update/{id}', 'modify')->middleware('can:admin_subscription_edit');
        Route::delete('/delete/{id}', 'delete')->middleware('can:admin_subscription_delete');
    });
    
    Route::controller(SubscriperController::class)->prefix('subscriper')
    ->group(function() {
        Route::get('/', 'view')->middleware('can:admin_subcriber_view');
        Route::get('/item/{id}', 'filter')->middleware('can:admin_subcriber_view');
        Route::post('/add', 'create')->middleware('can:admin_subcriber_add');
        Route::post('/update/{id}', 'modify')->middleware('can:admin_subcriber_edit');
        Route::delete('/delete/{id}', 'delete')->middleware('can:admin_subcriber_delete');
    });
    
    Route::controller(PaymentController::class)->prefix('payments')
    ->group(function() {
        Route::get('/', 'view')->middleware('can:admin_Payment_view');
        Route::put('/approve/{id}', 'approve')->middleware('can:admin_Payment_status');
        Route::put('/reject/{id}', 'reject')->middleware('can:admin_Payment_status');
    });
    
    Route::controller(InvoiceController::class)->prefix('invoice')
    ->group(function() {
        Route::get('village/{id}', 'invoice_village')->middleware('can:admin_Invoice_view');
        Route::get('provider/{id}', 'invoice_provider')->middleware('can:admin_Invoice_view');
    });

    Route::controller(ProfileController::class)->prefix('profile')
    ->group(function() {
        Route::get('/', 'profile');
        Route::post('/update_profile', 'update_profile');
    });
});