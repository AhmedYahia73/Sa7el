<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SubscriperRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    { 
        return [
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'package_id' => ['required', 'exists:packages,id'],
            'service_id' => ['required_if:type,provider', 'exists:service_types,id'],
            'provider_id' => ['required_if:type,provider', 'exists:providers,id'],
            'village_id' => ['required_if:type,village', 'exists:villages,id'],
            'm_provider_id' => ['required_if:type,maintenance_provider', 'exists:service_providers,id'],
        ];
    }

    public function failedValidation(Validator $validator){
       throw new HttpResponseException(response()->json([
               'message'=>'validation error',
               'errors'=>$validator->errors(),
       ],400));
   }
}
