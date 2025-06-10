<?php

namespace App\Http\Controllers\api\SuperAdmin\payment_method;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\SuperAdmin\PaymentMethodRequest;
use App\trait\TraitImage;

use App\Models\PaymentMethod;

class PaymentMethodController extends Controller
{
    public function __construct(private PaymentMethod $payment_method){}
    use TraitImage;

    public function view(){
        $payment_method = $this->payment_method
        ->with('translations')
        ->get();

        return response()->json([
            'payment_methods' => $payment_method,
        ]);
    }

    public function payment_method($id){
        $payment_method = $this->payment_method
        ->with('translations')
        ->where('id', $id)
        ->first();

        return response()->json([
            'payment_method' => $payment_method,
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
        
        $payment_method = $this->payment_method
        ->where('id', $id)
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned'
        ]);
    }

    public function create(PaymentMethodRequest $request){
        // logo, name, description, status
        // ar_name, ar_description
        $validator = Validator::make($request->all(), [
            'logo' => ['required'],
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $paymentMethodRequest = $request->validated();
        if (!is_string($request->logo)) {
            $image_path = $this->upload($request, 'logo', 'images/payment_methods');
            $paymentMethodRequest['logo'] = $image_path;
        }
        $payment_method = $this->payment_method
        ->create($paymentMethodRequest);
        $payment_method_translations = [[ 
            'locale' => 'en',
            'key' => 'name',
            'value' => $request->name,
        ]];
        if (!empty($request->ar_name)) {
            $payment_method_translations[] = [ 
                'locale' => 'ar',
                'key' => 'name',
                'value' => $request->ar_name,
            ];
        }
        if (!empty($request->description)) {
            $payment_method_translations[] = [ 
                'locale' => 'en',
                'key' => 'description',
                'value' => $request->description,
            ];
        }
        if (!empty($request->ar_description)) {
            $payment_method_translations[] = [ 
                'locale' => 'ar',
                'key' => 'description',
                'value' => $request->ar_description,
            ];
        }
        $payment_method->translations()->createMany($payment_method_translations);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(PaymentMethodRequest $request, $id){
        // logo, name, description, status
        // ar_name, ar_description
        $validator = Validator::make($request->all(), [
            'logo' => ['required'],
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $paymentMethodRequest = $request->validated();
        $payment_method = $this->payment_method
        ->where('id', $id)
        ->first();
        if (empty($payment_method)) {
            return response()->json([
                'errors' => 'payment_method not found'
            ], 400);
        }
        if (!is_string($request->logo)) {
            $image_path = $this->update_image($request, $payment_method->logo, 'logo', 'images/payment_methods');
            $paymentMethodRequest['logo'] = $image_path;
        }
        $payment_method
        ->update($paymentMethodRequest);
        $payment_method_translations = [[ 
            'locale' => 'en',
            'key' => 'name',
            'value' => $request->name,
        ]];
        if (!empty($request->ar_name)) {
            $payment_method_translations[] = [ 
                'locale' => 'ar',
                'key' => 'name',
                'value' => $request->ar_name,
            ];
        }
        if (!empty($request->description)) {
            $payment_method_translations[] = [ 
                'locale' => 'en',
                'key' => 'description',
                'value' => $request->description,
            ];
        }
        if (!empty($request->ar_description)) {
            $payment_method_translations[] = [ 
                'locale' => 'ar',
                'key' => 'description',
                'value' => $request->ar_description,
            ];
        }
        $payment_method->translations()->delete();
        $payment_method->translations()->createMany($payment_method_translations);

        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function delete($id){
        $payment_method = $this->payment_method
        ->where('id', $id)
        ->first();
        if (empty($payment_method)) {
            return response()->json([
                'errors' => 'payment_method not found'
            ], 400);
        }
        $payment_method->translations()->delete();
        $this->deleteImage($payment_method->logo);
        $payment_method->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
