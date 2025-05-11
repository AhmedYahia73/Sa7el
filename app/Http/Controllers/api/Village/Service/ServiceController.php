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
                'from' => $item->from,
                'to' => $item->to,
                'description' => $item->description,
                'service_type' => $item?->service?->name,
                'rate' => $item->rate,
            ];
        });

        return response()->json([
            'services' => $services,
        ]);
    }
}
