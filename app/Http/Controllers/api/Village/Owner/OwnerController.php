<?php

namespace App\Http\Controllers\api\Village\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\image;
use App\Http\Requests\Village\OwnerRequest;

use App\Models\VisitRequest;
use App\Models\User;
use App\Models\EntranceBeach;
use App\Models\EntranceGate;
use App\Models\EntrancePool;
use App\Models\Rent;
use App\Models\ProblemReport;
use App\Models\Maintenance;
use App\Models\AppartmentCode;

class OwnerController extends Controller
{
    public function __construct(private User $owners, 
    private AppartmentCode $appartment_code,
    private VisitRequest $visit_request){}
    use image;

    public function view(Request $request){
        $owners = $this->owners
        ->with('appartments', 'parent')
        ->whereHas('appartment_code', function($query) use($request){
            $query->where('type', 'owner')
            ->where('village_id', $request->user()->village_id);
        }) 
        ->get();
        $parent = $this->owners
        ->with('appartments')
        ->where('user_type', 'owner')
        ->where('user_type', 'owner')
        ->whereNull('parent_user_id')
        ->get();

        return response()->json([
            'owners' => $owners,
            'parents' => $parent,
        ]);
    }
 
}
