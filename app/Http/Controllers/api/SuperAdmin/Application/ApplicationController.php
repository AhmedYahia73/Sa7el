<?php

namespace App\Http\Controllers\api\SuperAdmin\Application;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Application;

class ApplicationController extends Controller
{
    public function view(){
        $data = Application::first();

        return response()->json([
            "app_description" => $data?->app_description
        ]);
    }

    public function update(Request $request){
        $validation = Validator::make($request->all(), [  
            'app_description' => ['required'],
            'google_api' => ['sometimes'],
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors(), 422);
        }
        $app = Application::first();
        $data = [];
        $data['app_description'] = $request->app_description;
        $data['google_api'] = $request->google_api;
        if($app){ 
            $app->update($data);
        }
        else{
            Application::create($data);
        }

        return response()->json([
            "success" => "You update data success"
        ]);
    }
}
