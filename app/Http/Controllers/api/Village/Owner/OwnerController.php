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
 
    public function owner(Request $request, $id){
        $owner = $this->owners
        ->with('appartments') 
        ->where('id', $id)
        ->with('parent')
        ->first();
        $entrance =  [
            'gates' => EntranceGate::with('gate')
            ->where('user_id', $id)
            ->where('village_id', $request->user()->village_id)->get(),
            'beaches' => EntranceBeach::with('beach')
            ->where('village_id', $request->user()->village_id)
            ->where('user_id', $id)->get(),
            'pools' => EntrancePool::with('pool')
            ->where('village_id', $request->user()->village_id)
            ->where('user_id', $id)->get(),
        ]; 
        $rent = $this->appartment_code
        ->where('village_id', $request->user()->village_id)
        ->where('owner_id', $id)
        ->with('appartment')
        ->get();
        $problem_request = ProblemReport::where('user_id', $id)
        ->where('village_id', $request->user()->village_id)->get();
        $maintenance_request = Maintenance::
        with('maintenance_type', 'appartment')
        ->where('village_id', $request->user()->village_id)
        ->where('user_id', $id)->get();
        $visit_requests = $this->visit_request
        ->where('village_id', $request->user()->village_id)
        ->where('owner_id', $id)
        ->with(['owner:id,name', 'appartment:id,unit'])
        ->get();

        return response()->json([
            'owner' => $owner,
            'entrance' => $entrance,
            'rent' => $rent,
            'problem_request' => $problem_request,
            'maintenance_request' => $maintenance_request,
            'visit_requests' => $visit_requests,
        ]);
    }
}
