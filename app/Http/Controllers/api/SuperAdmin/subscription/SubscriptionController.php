<?php

namespace App\Http\Controllers\api\SuperAdmin\subscription;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\SuperAdmin\SubscriptionRequest;

use App\Models\Package;

class SubscriptionController extends Controller
{
    public function __construct(private Package $package){}

    public function view(){
        $package = $this->package
        ->with('translations', 'service')
        ->get();

        return response()->json([
            'packages' => $package,
        ]);
    }

    public function package($id){
        $package = $this->package
        ->with('translations', 'service')
        ->where('id', $id)
        ->first();

        return response()->json([
            'package' => $package,
        ]);
    }

    public function status(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'status' => 'required|boolean',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        
        $package = $this->package
        ->where('id', $id)
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned'
        ]);
    }

    public function create(SubscriptionRequest $request){
        // name, description, price, type, feez
        // discount, admin_num, security_num, maintenance_module
        // beach_pool_module, status, service_id
        // ar_name, ar_description
        $packageRequest = $request->validated();
        $package = $this->package
        ->create($packageRequest);
        $package_translations = [[ 
            'locale' => 'en',
            'key' => 'name',
            'value' => $request->name,
        ]];
        if (!empty($request->ar_name)) {
            $package_translations[] = [ 
                'locale' => 'ar',
                'key' => 'name',
                'value' => $request->ar_name,
            ];
        }
        if (!empty($request->description)) {
            $package_translations[] = [ 
                'locale' => 'en',
                'key' => 'description',
                'value' => $request->description,
            ];
        }
        if (!empty($request->ar_description)) {
            $package_translations[] = [ 
                'locale' => 'ar',
                'key' => 'description',
                'value' => $request->ar_description,
            ];
        }
        $package->translations()->createMany($package_translations);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(SubscriptionRequest $request, $id){
       // name, description, price, type, feez
        // discount, admin_num, security_num, maintenance_module
        // beach_pool_module, status, service_id
        // ar_name, ar_description
        $packageRequest = $request->validated();
        $package = $this->package
        ->where('id', $id)
        ->first();
        if (empty($package)) {
            return response()->json([
                'errors' => 'package not found'
            ], 400);
        } 
        $package
        ->update($packageRequest);
        $package_translations = [[ 
            'locale' => 'en',
            'key' => 'name',
            'value' => $request->name,
        ]];
        if (!empty($request->ar_name)) {
            $package_translations[] = [ 
                'locale' => 'ar',
                'key' => 'name',
                'value' => $request->ar_name,
            ];
        }
        if (!empty($request->description)) {
            $package_translations[] = [ 
                'locale' => 'en',
                'key' => 'description',
                'value' => $request->description,
            ];
        }
        if (!empty($request->ar_description)) {
            $package_translations[] = [ 
                'locale' => 'ar',
                'key' => 'description',
                'value' => $request->ar_description,
            ];
        }
        $package->translations()->delete();
        $package->translations()->createMany($package_translations);

        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function delete($id){
        $package = $this->package
        ->where('id', $id)
        ->first();
        if (empty($package)) {
            return response()->json([
                'errors' => 'package not found'
            ], 400);
        }
        $package->translations()->delete();
        $package->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
