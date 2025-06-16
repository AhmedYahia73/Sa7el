<?php

namespace App\Providers\gates;
use Illuminate\Support\Facades\Gate;

class AdminGates
{
    public static function defineGates()
    {
        // ____________________ Zone ___________________________________________________
        Gate::define('admin_zone_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Zone')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_zone_status', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Zone')->whereIn('action', ['all', 'status'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_zone_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Zone')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_zone_edit', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Zone')->whereIn('action', ['all', 'edit'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_zone_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Zone')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ Village ___________________________________________________
        Gate::define('admin_Village_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Village')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_Village_status', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Village')->whereIn('action', ['all', 'status'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_Village_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Village')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_Village_edit', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Village')->whereIn('action', ['all', 'edit'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_Village_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Village')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_Village_update_profile', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Village')->whereIn('action', ['all', 'update_profile'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_Village_view_units', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Village')->whereIn('action', ['all', 'view_units'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_Village_delete_unit', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Village')->whereIn('action', ['all', 'delete_unit'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ village_gallery ___________________________________________________
        Gate::define('admin_village_gallery_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Village Gallery')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_village_gallery_status', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Village Gallery')->whereIn('action', ['all', 'status'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_village_gallery_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Village Gallery')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        }); 
        Gate::define('admin_village_gallery_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Village Gallery')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ village_admin ___________________________________________________
        Gate::define('admin_village_admin_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Village Admin')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_village_admin_status', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Village Admin')->whereIn('action', ['all', 'status'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_village_admin_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Village Admin')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_village_admin_edit', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Village Admin')->whereIn('action', ['all', 'edit'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_village_admin_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Village Admin')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ village_admin_role ___________________________________________________
        Gate::define('admin_village_admin_role_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Village Admin Role')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_village_admin_role_status', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Village Admin Role')->whereIn('action', ['all', 'status'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_village_admin_role_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Village Admin Role')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_village_admin_role_edit', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Village Admin Role')->whereIn('action', ['all', 'edit'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_village_admin_role_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Village Admin Role')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ village_cover ___________________________________________________
        Gate::define('admin_village_cover_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Village Cover')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        }); 
        Gate::define('admin_village_cover_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Village Cover')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        }); 
        Gate::define('admin_village_cover_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Village Cover')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ village_profile ___________________________________________________
        Gate::define('admin_village_profile_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Village Profile')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_village_profile_status', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Village Profile')->whereIn('action', ['all', 'status'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_village_profile_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Village Profile')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_village_profile_edit', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Village Profile')->whereIn('action', ['all', 'edit'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_village_profile_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Village Profile')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ appartment_type ___________________________________________________
        Gate::define('admin_appartment_type_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Appartment Type')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_appartment_type_status', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Appartment Type')->whereIn('action', ['all', 'status'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_appartment_type_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Appartment Type')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_appartment_type_edit', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Appartment Type')->whereIn('action', ['all', 'edit'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_appartment_type_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Appartment Type')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ user ___________________________________________________
        Gate::define('admin_user_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'User')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_user_status', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'User')->whereIn('action', ['all', 'status'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_user_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'User')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_user_edit', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'User')->whereIn('action', ['all', 'edit'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_user_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'User')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ service_type ___________________________________________________
        Gate::define('admin_service_type_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Service Type')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_service_type_status', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Service Type')->whereIn('action', ['all', 'status'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_service_type_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Service Type')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_service_type_edit', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Service Type')->whereIn('action', ['all', 'edit'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_service_type_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Service Type')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ provider ___________________________________________________
        Gate::define('admin_provider_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_status', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider')->whereIn('action', ['all', 'status'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_edit', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider')->whereIn('action', ['all', 'edit'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ provider_gallery ___________________________________________________
        Gate::define('admin_provider_gallery_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Gallery')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_gallery_status', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Gallery')->whereIn('action', ['all', 'status'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_gallery_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Gallery')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        }); 
        Gate::define('admin_provider_gallery_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Gallery')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ provider_admin ___________________________________________________
        Gate::define('admin_provider_admin_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Admin')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_admin_status', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Admin')->whereIn('action', ['all', 'status'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_admin_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Admin')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_admin_edit', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Admin')->whereIn('action', ['all', 'edit'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_admin_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Admin')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ provider_admin_role ___________________________________________________
        Gate::define('admin_provider_admin_role_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Admin Role')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_admin_role_status', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Admin Role')->whereIn('action', ['all', 'status'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_admin_role_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Admin Role')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_admin_role_edit', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Admin Role')->whereIn('action', ['all', 'edit'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_admin_role_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Admin Role')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ provider_cover ___________________________________________________
        Gate::define('admin_provider_cover_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Cover')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        }); 
        Gate::define('admin_provider_cover_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Cover')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_cover_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Cover')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ provider_profile ___________________________________________________
        Gate::define('admin_provider_profile_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Profile')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_profile_status', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Profile')->whereIn('action', ['all', 'status'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_profile_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Profile')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_profile_edit', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Profile')->whereIn('action', ['all', 'edit'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_profile_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Profile')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ payment_method ___________________________________________________
        Gate::define('admin_payment_method_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Payment Method')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_payment_method_status', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Payment Method')->whereIn('action', ['all', 'status'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_payment_method_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Payment Method')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_payment_method_edit', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Payment Method')->whereIn('action', ['all', 'edit'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_payment_method_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Payment Method')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ subscription ___________________________________________________
        Gate::define('admin_subscription_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Subscription')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_subscription_status', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Subscription')->whereIn('action', ['all', 'status'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_subscription_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Subscription')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_subscription_edit', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Subscription')->whereIn('action', ['all', 'edit'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_subscription_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Subscription')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ subcriber ___________________________________________________
        Gate::define('admin_subcriber_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'subcriber')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        }); 
        Gate::define('admin_subcriber_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'subcriber')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_subcriber_edit', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'subcriber')->whereIn('action', ['all', 'edit'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_subcriber_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'subcriber')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ Payment ___________________________________________________
        Gate::define('admin_Payment_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Payment')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_Payment_status', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Payment')->whereIn('action', ['all', 'status'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ Invoice ___________________________________________________
        Gate::define('admin_Invoice_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Invoice')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ Admin ___________________________________________________
        Gate::define('admin_Admin_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Admin')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_Admin_status', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Admin')->whereIn('action', ['all', 'status'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_Admin_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Admin')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_Admin_edit', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Admin')->whereIn('action', ['all', 'edit'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_Admin_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Admin')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ admin_role ___________________________________________________
        Gate::define('admin_admin_role_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Admin Role')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_admin_role_status', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Admin Role')->whereIn('action', ['all', 'status'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_admin_role_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Admin Role')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_admin_role_edit', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Admin Role')->whereIn('action', ['all', 'edit'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_admin_role_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Admin Role')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ maintenance_type ___________________________________________________
        Gate::define('admin_maintenance_type_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Maintenance Type')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_maintenance_type_status', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Maintenance Type')->whereIn('action', ['all', 'status'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_maintenance_type_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Maintenance Type')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_maintenance_type_edit', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Maintenance Type')->whereIn('action', ['all', 'edit'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_maintenance_type_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Maintenance Type')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ provider_maintenance ___________________________________________________
        Gate::define('admin_provider_maintenance_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Maintenance')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_maintenance_status', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Maintenance')->whereIn('action', ['all', 'status'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_maintenance_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Maintenance')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_maintenance_edit', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Maintenance')->whereIn('action', ['all', 'edit'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_maintenance_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Maintenance')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ provider_maintenance_gallery ___________________________________________________
        Gate::define('admin_provider_maintenance_gallery_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Maintenance Gallery')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_maintenance_gallery_status', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Maintenance Gallery')->whereIn('action', ['all', 'status'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_maintenance_gallery_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Maintenance Gallery')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        }); 
        Gate::define('admin_provider_maintenance_gallery_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Maintenance Gallery')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ provider_maintenance_admin ___________________________________________________
        Gate::define('admin_provider_maintenance_admin_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Maintenance Admin')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_maintenance_admin_status', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Maintenance Admin')->whereIn('action', ['all', 'status'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_maintenance_admin_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Maintenance Admin')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_maintenance_admin_edit', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Maintenance Admin')->whereIn('action', ['all', 'edit'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_maintenance_admin_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Maintenance Admin')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ provider_maintenance_admin_role ___________________________________________________
        Gate::define('admin_provider_maintenance_admin_role_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Maintenance Admin Role')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_maintenance_admin_role_status', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Maintenance Admin Role')->whereIn('action', ['all', 'status'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_maintenance_admin_role_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Maintenance Admin Role')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_maintenance_admin_role_edit', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Maintenance Admin Role')->whereIn('action', ['all', 'edit'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_provider_maintenance_admin_role_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Maintenance Admin Role')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ provider_maintenance_cover ___________________________________________________
        Gate::define('admin_provider_maintenance_cover_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Maintenance Cover')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        }); 
        Gate::define('admin_provider_maintenance_cover_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Maintenance Cover')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        }); 
        Gate::define('admin_provider_maintenance_cover_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Provider Maintenance Cover')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ Mall ___________________________________________________
        Gate::define('admin_Mall_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Mall')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_Mall_update_profile', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Mall')->whereIn('action', ['all', 'update_profile'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_Mall_status', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Mall')->whereIn('action', ['all', 'status'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_Mall_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Mall')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_Mall_edit', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Mall')->whereIn('action', ['all', 'edit'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_Mall_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Mall')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ mall_gallery ___________________________________________________
        Gate::define('admin_mall_gallery_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Mall Gallery')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_mall_gallery_status', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Mall Gallery')->whereIn('action', ['all', 'status'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_mall_gallery_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Mall Gallery')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        }); 
        Gate::define('admin_mall_gallery_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Mall Gallery')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ mall_admin ___________________________________________________
        Gate::define('admin_mall_admin_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Mall Admin')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_mall_admin_status', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Mall Admin')->whereIn('action', ['all', 'status'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_mall_admin_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Mall Admin')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_mall_admin_edit', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Mall Admin')->whereIn('action', ['all', 'edit'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_mall_admin_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Mall Admin')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ mall_admin_role ___________________________________________________
        Gate::define('admin_mall_admin_role_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Mall Admin Role')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_mall_admin_role_status', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Mall Admin Role')->whereIn('action', ['all', 'status'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_mall_admin_role_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Mall Admin Role')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_mall_admin_role_edit', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Mall Admin Role')->whereIn('action', ['all', 'edit'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_mall_admin_role_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Mall Admin Role')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ mall_cover ___________________________________________________
        Gate::define('admin_mall_cover_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Mall Cover')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        }); 
        Gate::define('admin_mall_cover_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Mall Cover')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        }); 
        Gate::define('admin_mall_cover_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Mall Cover')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ mall_profile ___________________________________________________
        Gate::define('admin_mall_profile_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Mall Profile')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_mall_profile_status', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Mall Profile')->whereIn('action', ['all', 'status'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_mall_profile_add', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Mall Profile')->whereIn('action', ['all', 'add'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_mall_profile_edit', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Mall Profile')->whereIn('action', ['all', 'edit'])->first())) {
                return true;
            }
            return false;
        });
        Gate::define('admin_mall_profile_delete', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Mall Profile')->whereIn('action', ['all', 'delete'])->first())) {
                return true;
            }
            return false;
        });
        // ____________________ Home ___________________________________________________
        Gate::define('admin_Home_view', function ($user) {
            if (!$user->position && !empty($user->position->sup_roles->where('module', 'Home')->whereIn('action', ['all', 'view'])->first())) {
                return true;
            }
            return false;
        });
    }
}
