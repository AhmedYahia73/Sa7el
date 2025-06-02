<?php

namespace App\Http\Controllers\api\SuperAdmin\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Village;
use App\Models\User;
use App\Models\Appartment;
use App\Models\Payment;
use App\Models\Provider;
use App\Models\ServiceProvider;

class HomeController extends Controller
{
    public function __construct(private Village $villages,
    private User $users, private Appartment $units, 
    private Payment $payment, private Provider $provider,
    private ServiceProvider $service_provider){}

    public function view(){
        $villages = $this->villages
        ->count();
        $users = $this->users
        ->where('role', 'user')
        ->count();
        $units = $this->units 
        ->count();
        $subscriper = $this->villages
        ->where('to', '>=', date('Y-m-d'))
        ->count();
        $subscriper += $this->provider
        ->where('to', '>=', date('Y-m-d'))
        ->count();
        $subscriper += $this->service_provider
        ->where('to', '>=', date('Y-m-d'))
        ->count();
        $pending_payment = $this->payment
        ->where('status', 'pending')
        ->count();
        $provider += $this->provider
        ->count();
        $maintenance_providers += $this->service_provider
        ->count();

        return response()->json([
            'villages' => $villages,
            'users' => $users,
            'units' => $units,
            'subscriper' => $subscriper,
            'pending_payment' => $pending_payment,
            'service_providers' => $provider,
            'maintenance_providers' => $maintenance_providers,
        ]);
    }
}
