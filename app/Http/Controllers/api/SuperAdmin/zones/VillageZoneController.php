<?php

namespace App\Http\Controllers\api\SuperAdmin\zones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\ZoneVillage;
use App\Models\Village;

class VillageZoneController extends Controller
{
    public function list()
    {
        $villages = Village::select('id', 'name')->where('status', 1)->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "name" => $item->name,
            ];
        });

        return response()->json([
            'villages' => $villages,
        ]);
    }

    public function view(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'search'         => 'sometimes', 
            'village_id'      => 'sometimes|exists:villages,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        $zones = ZoneVillage::with('village:id,name')
            ->when($request->village_id, fn($q) => $q->where('village_id', $request->village_id))
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($q) use ($request) {
                    $q->whereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%{$request->search}%"])
                      ->orWhereRaw("JSON_EXTRACT(name, '$.ar') LIKE ?", ["%{$request->search}%"])
                      ->orWhereRaw("JSON_EXTRACT(description, '$.en') LIKE ?", ["%{$request->search}%"])
                      ->orWhereRaw("JSON_EXTRACT(description, '$.ar') LIKE ?", ["%{$request->search}%"]);
                });
            })
            ->paginate($request->get('per_page', 10));

        return response()->json($zones);
    }

    public function show($id)
    {
        $zone = ZoneVillage::with('village:id,name')->where('id', $id)->first();

        if (empty($zone)) {
            return response()->json(['errors' => 'zone not found'], 404);
        }

        return response()->json(['zone' => $zone]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'            => 'required|array',
            'name.en'         => 'required',
            'name.ar'         => 'required',
            'description'     => 'array',
            'description.en'  => 'required',
            'description.ar'  => 'required',
            'lat'             => 'required|numeric',
            'lng'             => 'required|numeric',
            'village_id'      => 'required|exists:villages,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        ZoneVillage::create([
            'name'        => $request->name,
            'description' => $request->description,
            'lat'         => $request->lat,
            'lng'         => $request->lng,
            'village_id'  => $request->village_id,
        ]);

        return response()->json(['success' => 'You add data success']);
    }

    public function modify(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'            => 'required|array',
            'name.en'         => 'required',
            'name.ar'         => 'required',
            'description'     => 'array',
            'description.en'  => 'required',
            'description.ar'  => 'required',
            'lat'             => 'required|numeric',
            'lng'             => 'required|numeric',
            'village_id'      => 'required|exists:villages,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $zone = ZoneVillage::where('id', $id)->first();

        if (empty($zone)) {
            return response()->json(['errors' => 'zone not found'], 404);
        }

        $zone->update([
            'name'        => $request->name,
            'description' => $request->description,
            'lat'         => $request->lat,
            'lng'         => $request->lng,
            'village_id'  => $request->village_id,
        ]);

        return response()->json(['success' => 'You update data success']);
    }

    public function delete($id)
    {
        $zone = ZoneVillage::where('id', $id)->first();

        if (empty($zone)) {
            return response()->json(['errors' => 'zone not found'], 404);
        }

        $zone->delete();

        return response()->json(['success' => 'You delete data success']);
    }
}
