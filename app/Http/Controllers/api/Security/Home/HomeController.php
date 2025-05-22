<?php

namespace App\Http\Controllers\api\Security\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Beach;
use App\Models\Pools;
use App\Models\Gate;

class HomeController extends Controller
{
    public function __construct(private Beach $beaches,
    private Pools $pools, private Gate $gates){}

    public function view(Request $request){
        $beaches = $this->beaches
        ->where('village_id', $request->user()->village_id)
        ->get();
        $pools = $this->pools
        ->where('village_id', $request->user()->village_id)
        ->get();
        $gates = $this->gates
        ->where('village_id', $request->user()->village_id)
        ->get();

        return response()->json([
            'beaches' => $beaches,
            'pools' => $pools,
            'gates' => $gates,
        ]);
    }
}
