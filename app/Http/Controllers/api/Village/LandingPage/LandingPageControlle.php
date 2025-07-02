<?php

namespace App\Http\Controllers\api\Village\LandingPage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Village;

class LandingPageControlle extends Controller
{
    public function __construct(private Village $village){}

    public function lists(Request $request){
        $villages = $this->village
        ->select('id', 'name')
        ->get();

        return response()->json([
            'villages' => $villages
        ]);
    }

    public function view(Request $request, $id){
        $village = $this->village
        ->with('gallery', 'zone:id,name')
        ->where('id', $id)
        ->first();
        $village->apple_app = 'https://apps.apple.com/eg/app/sea-go-services/id6746714190';
        $village->android_app = 'https://play.google.com/store/apps/details?id=com.app.seago&pcampaignid=web_share';

        return response()->json([
            'village' => $village
        ]);
    }
}
