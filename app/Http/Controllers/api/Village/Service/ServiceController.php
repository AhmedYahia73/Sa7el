<?php

namespace App\Http\Controllers\api\Village\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Provider;

class ServiceController extends Controller
{
    public function __construct(private Provider $provider){}

    public function view(Request $request){
        $services = $this->provider
        ->with('service')
        ->where('village_id', $request->user()->id)
        ->get()
        ->map(function($item){
            return [
                'id' => $item->id,
                'name' => $item->name,
                'phone' => $item->phone,
                'image' => $item->image_link,
                'location' => $item->location,
                'from' => $item->open_from,
                'to' => $item->open_to,
                'description' => $item->description,
                'service_type' => $item?->service?->name,
                'rate' => $item->rate,
                'ar_name' => $item->ar_name,
                'ar_description' => $item->ar_description,
            ];
        });

        return response()->json([
            'services' => $services,
        ]);
    }
}
