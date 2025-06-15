<?php

namespace App\Providers\gates;
use Illuminate\Support\Facades\Gate;
 

class Gates
{
    public static function defineGates()
    {
        // Gate::define('package_roles', function ($user) { 
        //     $village = $user->village;
        //     $from = $village->from;
        //     $to = $village->to;
        //     if ($from <= date('Y-m-d') && $to >= date('Y-m-d')) {
        //         return true;
        //     }
        //     return false;
        // });
        
        Gate::define('Settings', function ($user) { 
            if ($user->position &&
            $user->position->roles->pluck('module')->contains('Settings')) {
                return true;
            }
            return false;
        });
        Gate::define('owner', function ($user) { 
            if ($user->position &&
            $user->position->roles->pluck('module')->contains('Owner')) {
                return true;
            }
            return false;
        });
        Gate::define('Pool', function ($user) { 
            if ($user->position && $user->village->package->beach_pool_module ?? 0 &&
            $user->position->roles->pluck('module')->contains('Pool')) {
                return true;
            }
            return false;
        });
        Gate::define('Beach', function ($user) { 
            if ($user->position && $user->village->package->beach_pool_module ?? 0 &&  
            $user->position->roles->pluck('module')->contains('Beach')) {
                return true;
            }
            return false;
        });
        Gate::define('Services', function ($user) { 
            if ($user->position &&  
            $user->position->roles->pluck('module')->contains('Services')) {
                return true;
            }
            return false;
        });
        Gate::define('Problem Reports', function ($user) { 
            if ($user->position &&  
            $user->position->roles->pluck('module')->contains('Problem Reports')) {
                return true;
            }
            return false;
        });
        Gate::define('Maintenance Request', function ($user) { 
            if ($user->position &&  
            $user->position->roles->pluck('module')->contains('Maintenance Request')) {
                return true;
            }
            return false;
        });
        Gate::define('Visits', function ($user) { 
            if ($user->position &&  
            $user->position->roles->pluck('module')->contains('Visits')) {
                return true;
            }
            return false;
        });
        Gate::define('Gates', function ($user) { 
            if ($user->position &&  
            $user->position->roles->pluck('module')->contains('Gates')) {
                return true;
            }
            return false;
        });
        Gate::define('Security Man', function ($user) { 
            if ($user->position &&  
            $user->position->roles->pluck('module')->contains('Security Man')) {
                return true;
            }
            return false;
        });
        Gate::define('Maintenance Fees', function ($user) { 
            if ($user->position && $user->village->package->maintenance_module &&
            $user->position->roles->pluck('module')->contains('Maintenance Fees')) {
                return true;
            }
            return false;
        });
        Gate::define('Appartment', function ($user) { 
            if ($user->position &&  
            $user->position->roles->pluck('module')->contains('Appartment')) {
                return true;
            }
            return false;
        });
        Gate::define('For Rent & Sale', function ($user) { 
            if ($user->position &&  
            $user->position->roles->pluck('module')->contains('For Rent & Sale')) {
                return true;
            }
            return false;
        });
        Gate::define('Rent', function ($user) { 
            if ($user->position &&  
            $user->position->roles->pluck('module')->contains('Rent')) {
                return true;
            }
            return false;
        });
        Gate::define('Village Page', function ($user) { 
            if ($user->position &&  
            $user->position->roles->pluck('module')->contains('Village Page')) {
                return true;
            }
            return false;
        });
        Gate::define('News Feed', function ($user) { 
            if ($user->position &&  
            $user->position->roles->pluck('module')->contains('News Feed')) {
                return true;
            }
            return false;
        });
        Gate::define('Maintenance Type', function ($user) { 
            if ($user->position &&  
            $user->position->roles->pluck('module')->contains('Maintenance Type')) {
                return true;
            }
            return false;
        });
        Gate::define('Payment', function ($user) { 
            if ($user->position &&  
            $user->position->roles->pluck('module')->contains('Payment')) {
                return true;
            }
            return false;
        });
        Gate::define('Home', function ($user) { 
            if ($user->position &&  
            $user->position->roles->pluck('module')->contains('Home')) {
                return true;
            }
            return false;
        });
        Gate::define('Service Type', function ($user) { 
            if ($user->position &&  
            $user->position->roles->pluck('module')->contains('Service Type')) {
                return true;
            }
            return false;
        });
    }
}
