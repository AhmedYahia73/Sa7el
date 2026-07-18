<?php

use App\Http\Controllers\api\SuperAdmin\Admin\AdminController;
use App\Http\Controllers\api\SuperAdmin\Admin\AdminRoleController;
use App\Http\Controllers\api\SuperAdmin\appartment_type\AppartmentTypeController;
use App\Http\Controllers\api\SuperAdmin\Help\HelpGroupController;
use App\Http\Controllers\api\SuperAdmin\Help\HelpVideoController;
use App\Http\Controllers\api\SuperAdmin\Home\HomeController;
use App\Http\Controllers\api\SuperAdmin\invoice\InvoiceController;
use App\Http\Controllers\api\SuperAdmin\MaintenanceType\MaintenanceTypeController;
use App\Http\Controllers\api\SuperAdmin\Mall\MallController;
use App\Http\Controllers\api\SuperAdmin\Mall\MallCoverController;
use App\Http\Controllers\api\SuperAdmin\Mall\MallGallaryController;
use App\Http\Controllers\api\SuperAdmin\payment_method\PaymentMethodController;
use App\Http\Controllers\api\SuperAdmin\payment\PaymentController;
use App\Http\Controllers\api\SuperAdmin\Provider\ProviderAdminController;
use App\Http\Controllers\api\SuperAdmin\Provider\ProviderController;
use App\Http\Controllers\api\SuperAdmin\Provider\ProviderCoverController;
use App\Http\Controllers\api\SuperAdmin\Provider\ProviderGalleryController;
use App\Http\Controllers\api\SuperAdmin\Provider\ProviderRolesController;
use App\Http\Controllers\api\SuperAdmin\Requests\RequestController;
use App\Http\Controllers\api\SuperAdmin\service_type\ServiceTypeController;
use App\Http\Controllers\api\SuperAdmin\ServiceProvider\MaintenanceProviderAdminController;
use App\Http\Controllers\api\SuperAdmin\ServiceProvider\MaintenanceProviderCoverController;
use App\Http\Controllers\api\SuperAdmin\ServiceProvider\MaintenanceProviderGalleryController;
use App\Http\Controllers\api\SuperAdmin\ServiceProvider\MaintenanceProviderRolesController;
use App\Http\Controllers\api\SuperAdmin\ServiceProvider\ServiceProviderController;
use App\Http\Controllers\api\SuperAdmin\subscriper\SubscriperController;
use App\Http\Controllers\api\SuperAdmin\subscription\SubscriptionController;
use App\Http\Controllers\api\SuperAdmin\users\UserController;
use App\Http\Controllers\api\SuperAdmin\village\VillageAdminController;
use App\Http\Controllers\api\SuperAdmin\village\VillageController;
use App\Http\Controllers\api\SuperAdmin\village\VillageCoverController;
use App\Http\Controllers\api\SuperAdmin\village\VillageGallaryController;
use App\Http\Controllers\api\SuperAdmin\village\VillageRolesController;
use App\Http\Controllers\api\SuperAdmin\zones\ZoneController;
use App\Http\Controllers\api\SuperAdmin\zones\VillageZoneController;
use App\Http\Controllers\api\SuperAdmin\Application\ApplicationController;
use App\Http\Controllers\api\SuperAdmin\Popup\PopupController;
use App\Http\Controllers\api\SuperAdmin\village\AppartmentController;
use App\Http\Controllers\api\SuperAdmin\notification\NotificationController;
use App\Http\Controllers\api\SuperAdmin\Provider\ProviderReviewController;
use App\Http\Controllers\api\SuperAdmin\Verification\VerificationRequestController;
use App\Http\Controllers\api\User\Profile\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Broadcast;

Broadcast::routes(['middleware' => ['auth:sanctum']]);

Route::middleware(['auth:sanctum', 'IsAdmin'])->group(function(){
    Route::controller(HomeController::class)->prefix('home')
    ->group(function() {
        Route::get('/', 'view')->middleware('can:admin_Home_view'); 
    });

    Route::controller(NotificationController::class)
    ->prefix('push_notification')->group(function() {
        Route::post('/lists', 'lists');
        Route::post('/', 'push_notification');
    });

    Route::controller(ApplicationController::class)->prefix('application')
    ->group(function() {
        Route::get('/', 'view');  
        Route::post('/update', 'update');  
    });

    Route::controller(RequestController::class)->prefix('login_request')
    ->group(function() {
        Route::get('/', 'login_request');  
        Route::put('/status/{id}', 'login_request_status');  
    });

    Route::controller(RequestController::class)->prefix('code_request')
    ->group(function() {
        Route::get('/', 'code_request');  
        Route::put('/status/{id}', 'code_request_status');  
    });

    Route::controller(AppartmentController::class)->prefix('appartment')
    ->group(function() {
        Route::get('/appartement_list', 'appartement_list');
        Route::get('/user_list', 'user_list');
        Route::get('/view_codes/{id}', 'view_codes');
        Route::post('/delete_user_appartment', 'delete_user_appartment');
        Route::post('/create_code', 'create_code');
        Route::post('/update_code/{id}', 'update_code');
        Route::post('/add', 'create');
        Route::put('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
        Route::get('/appartement_details/{id}', 'appartement_details');
        Route::get('/all_units', 'all_units');
        Route::get('/unit_renters', 'unit_renters');
        Route::get('/unit_owners', 'unit_owners');
        Route::get('/unit_report', 'unit_report');
        Route::get('/village_list', 'village_list');
        Route::get('/{id}', 'view');
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

    Route::controller(VillageZoneController::class)->prefix('village_zone')
    ->group(function() {
        Route::get('/list', 'list');
        Route::get('/', 'view');
        Route::get('/item/{id}', 'show');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
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
        Route::get('/providers', 'provider_mall')->middleware('can:admin_Mall_providers');
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
    
    Route::controller(VerificationRequestController::class)
    ->prefix('verification_request')
    ->group(function() {
        Route::get('/', 'index');
        Route::put('/status/{id}', 'status');
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
        Route::get('/my_data', 'my_data')->middleware('can:admin_Admin_view');
        Route::put('/status/{id}', 'status')->middleware('can:admin_Admin_status');
        Route::post('/add', 'create')->middleware('can:admin_Admin_add');
        Route::post('/update/{id}', 'modify')->middleware('can:admin_Admin_edit');
        Route::delete('/delete/{id}', 'delete')->middleware('can:admin_Admin_delete');
    });
    
    Route::controller(VillageController::class)->prefix('village')
    ->group(function() {
        Route::get('/', 'view')->middleware('can:admin_Village_view');
        Route::get('/village_zones/{id}', 'village_zones')->middleware('can:admin_Village_view');
        Route::get('/item/{id}', 'village')->middleware('can:admin_Village_view');
        Route::put('/status/{id}', 'status')->middleware('can:admin_Village_status');
        Route::post('/update_profile_image/{id}', 'update_profile_image')->middleware('can:admin_Village_update_profile');
        Route::post('/add', 'create')->middleware('can:admin_Village_add');
        Route::post('/update/{id}', 'modify')->middleware('can:admin_Village_edit');
        Route::delete('/delete/{id}', 'delete')->middleware('can:admin_Village_delete');

        Route::post('/village_units', 'village_units')->middleware('can:admin_Village_view_units');
        Route::post('/village_units_delete', 'village_units_delete')->middleware('can:admin_Village_delete_unit');

        Route::get('/gate_keeper/{id}', 'gate_keeper')->middleware('can:admin_Village_gate_keeper');
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
        Route::get('/village_active', 'village_active')->middleware('can:logout_village');
        Route::get('/logout_village/{id}', 'logout_village')->middleware('can:logout_village');
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
        Route::get('/user_active', 'user_active')->middleware('can:admin_user_logout_user');
        Route::get('/online_user_units/{id}', 'online_user_units')->middleware('can:admin_user_logout_user');
        Route::get('/logout_user/{id}', 'logout_user')->middleware('can:admin_user_logout_user');
        Route::get('/', 'view')->middleware('can:admin_user_view');
        Route::post('/favourite_provider', 'favourite_provider')->middleware('can:admin_user_view');
        Route::get('/users', 'users')->middleware('can:admin_user_view');
        Route::get('/item/{id}', 'user')->middleware('can:admin_user_single_page');
        Route::get('/units/{id}', 'units')->middleware('can:admin_user_single_page');
        Route::put('/status/{id}', 'status')->middleware('can:admin_user_status');
        Route::post('/add', 'create')->middleware('can:admin_user_add');
        Route::post('/update/{id}', 'modify')->middleware('can:admin_user_edit');
        Route::delete('/delete/{id}', 'delete')->middleware('can:admin_user_delete');
        Route::post('/delete_user', 'delete_user')->middleware('can:admin_user_delete');
        Route::get('/favourite_users', 'favourite_users')->middleware('can:admin_user_view');
        Route::get('/make_user_favourite/{id}', 'make_user_favourite')->middleware('can:admin_user_edit');
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
        Route::get('/', 'view')->middleware('can:admin_provider_view');
        Route::get('/lists', 'lists')->middleware('can:admin_provider_view');
        Route::get('/item/{id}', 'provider')->middleware('can:admin_provider_view');
        Route::put('/status/{id}', 'status')->middleware('can:admin_provider_status');
        Route::post('/update_profile_image/{id}', 'update_profile_image')->middleware('can:admin_provider_profile');
        Route::post('/add', 'create')->middleware('can:admin_provider_add');
        Route::post('/update/{id}', 'modify')->middleware('can:admin_provider_edit');
        Route::delete('/delete/{id}', 'delete')->middleware('can:admin_provider_delete');
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
    
    Route::controller(ProviderReviewController::class)->prefix('show_reviews')
    ->group(function() {
        Route::get('/', 'show_reviews')->middleware('can:admin_provider_review_view');
        Route::delete('/delete_review/{id}', 'delete_review')->middleware('can:admin_provider_review_delete');
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
        Route::get('maintenance_provider/{id}', 'invoice_maintenance_provider')->middleware('can:admin_Invoice_view');
    });

    Route::controller(ProfileController::class)->prefix('profile')
    ->group(function() {
        Route::get('/', 'profile');
        Route::post('/update_profile', 'update_profile');
    });

    Route::controller(HelpGroupController::class)->prefix('help_group')
    ->group(function() {
        Route::get('/', 'view');
        Route::get('/item/{id}', 'show');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });

    Route::controller(HelpVideoController::class)->prefix('help_video')
    ->group(function() {
        Route::get('/', 'view');
        Route::get('/lists', 'lists');
        Route::get('/item/{id}', 'show');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });

    Route::controller(PopupController::class)->prefix('popup')
    ->group(function() {
        Route::get('/', 'view');
        Route::get('/lists', 'lists');
        Route::get('/item/{id}', 'show');
        Route::put('/status/{id}', 'status');
        Route::post('/add', 'create');
        Route::post('/update/{id}', 'modify');
        Route::delete('/delete/{id}', 'delete');
    });
});