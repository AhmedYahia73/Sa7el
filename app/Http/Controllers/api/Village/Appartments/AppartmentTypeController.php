<?php

namespace App\Http\Controllers\api\Village\Appartments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\AppartmentTypeUmbrella;
use App\Models\AppartmentType;
use App\Models\Village;

class AppartmentTypeController extends Controller
{
    public function list(Request $request)
    {
        $village_id = $request->user()->village_id;

        $villages = Village::where('status', 1)
        ->select('id', 'name')
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "name" => $item->name,
            ];
        });;

        $appartment_types = AppartmentType::where('status', 1)
        ->select('id', 'name')
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "name" => $item->name,
            ];
        });;

        return response()->json([
            'villages'         => $villages,
            'appartment_types' => $appartment_types,
        ]);
    }

    public function view(Request $request)
    {
        $village_id = $request->user()->village_id;

        $umbrellas = AppartmentTypeUmbrella::with([
            'type:id,name',
        ])
        ->where('village_id', $village_id)
        ->get()
        ->map(function($item){
            return [
                "village" => [
                    "id" => $item?->village?->id,
                    "name" => $item?->village?->name,
                ],
            ];
        });

        return response()->json([
            'umbrellas' => $umbrellas,
        ]);
    }

    public function create(Request $request)
    {
        $village_id = $request->user()->village_id;

        $validator = Validator::make($request->all(), [
            'appartment_type_id' => 'required|exists:appartment_types,id',
            'umbrellas'          => 'required|integer|min:0',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $exists = AppartmentTypeUmbrella::where('village_id', $village_id)
            ->where('appartment_type_id', $request->appartment_type_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'errors' => 'This apartment type already has an umbrella record for this village'
            ], 400);
        }

        AppartmentTypeUmbrella::create([
            'village_id'         => $village_id,
            'appartment_type_id' => $request->appartment_type_id,
            'umbrellas'          => $request->umbrellas,
        ]);

        return response()->json(['success' => 'You add data success']);
    }

    public function modify(Request $request, $id)
    {
        $village_id = $request->user()->village_id;

        $validator = Validator::make($request->all(), [
            'appartment_type_id' => 'required|exists:appartment_types,id',
            'umbrellas'          => 'required|integer|min:0',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $umbrella = AppartmentTypeUmbrella::where('id', $id)
            ->where('village_id', $village_id)
            ->first();

        if (empty($umbrella)) {
            return response()->json(['errors' => 'record not found'], 404);
        }

        // check duplicate only if type changed
        if ($umbrella->appartment_type_id != $request->appartment_type_id) {
            $exists = AppartmentTypeUmbrella::where('village_id', $village_id)
                ->where('appartment_type_id', $request->appartment_type_id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'errors' => 'This apartment type already has an umbrella record for this village'
                ], 400);
            }
        }

        $umbrella->update([
            'appartment_type_id' => $request->appartment_type_id,
            'umbrellas'          => $request->umbrellas,
        ]);

        return response()->json(['success' => 'You update data success']);
    }

    public function delete($id, Request $request)
    {
        $village_id = $request->user()->village_id;

        $umbrella = AppartmentTypeUmbrella::where('id', $id)
            ->where('village_id', $village_id)
            ->first();

        if (empty($umbrella)) {
            return response()->json(['errors' => 'record not found'], 404);
        }

        $umbrella->delete();

        return response()->json(['success' => 'You delete data success']);
    }
}
