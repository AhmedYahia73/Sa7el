<?php

namespace App\Providers\gates;
use Illuminate\Support\Facades\Gate;

class AdminGates
{
    public static function defineGates()
    {
        Gate::define('Admin_Admin', function ($user) { 
            if (!$user->provider_only) {
                return true;
            }
            return false;
        });
        
        Gate::define('Admin_Provider_View', function ($user) { 
            if (!$user->provider_only || ($user->provider_only && $user->super_roles->pluck('action')->contains('view'))) {
                return true;
            }
            return false;
        });
        
        Gate::define('Admin_Provider_Status', function ($user) { 
            if (!$user->provider_only || ($user->provider_only && $user->super_roles->pluck('action')->contains('status'))) {
                return true;
            }
            return false;
        });
        
        Gate::define('Admin_Provider_Add', function ($user) { 
            if (!$user->provider_only || ($user->provider_only && $user->super_roles->pluck('action')->contains('add'))) {
                return true;
            }
            return false;
        });
        
        Gate::define('Admin_Provider_Update', function ($user) { 
            if (!$user->provider_only || ($user->provider_only && $user->super_roles->pluck('action')->contains('edit'))) {
                return true;
            }
            return false;
        });
        
        Gate::define('Admin_Provider_Delete', function ($user) { 
            if (!$user->provider_only || ($user->provider_only && $user->super_roles->pluck('action')->contains('delete'))) {
                return true;
            }
            return false;
        });
    }
}
