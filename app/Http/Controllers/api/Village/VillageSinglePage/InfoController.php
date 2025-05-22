<?php

namespace App\Http\Controllers\api\Village\VillageSinglePage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Village;

class InfoController extends Controller
{
    public function __construct(private Village $village){}

    public function view(Request $request){
        $village = $this->village
        ->where('id', $request->user()->village_id)
        ->with('zone')
        ->withCount(['units', 'population'])
        ->first();

        return response()->json([
            'village' => $village
        ]);
    }
}
