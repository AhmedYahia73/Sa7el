<?php

namespace App\Http\Controllers\api\Village\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Appartment;
use App\Models\Maintenance;
use App\Models\ProblemReport;
use App\Models\AppartmentCode;

class HomeController extends Controller
{
    public function __construct(private Appartment $units,
    private Maintenance $mainten){}

    public function view(Request $request){

    }
}
