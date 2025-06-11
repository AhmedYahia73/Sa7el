<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SubscriptionRequest extends FormRequest
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
            'service_id' => ['exists:service_types,id'],
            'name' => ['required'],
            'description' => ['nullable'],
            'price' => ['required', 'numeric'],
            'type' => ['required', 'in:provider,village,maintenance_provider'],
            'feez' => ['required', 'numeric'],
            'discount' => ['required', 'numeric'],
            'admin_num' => ['numeric'],
            'security_num' => ['numeric'],
            'maintenance_module' => ['boolean'],
            'beach_pool_module' => ['boolean'],
            'status' => ['required', 'boolean'],
        ];
    }

    public function failedValidation(Validator $validator){
       throw new HttpResponseException(response()->json([
               'message'=>'validation error',
               'errors'=>$validator->errors(),
       ],400));
   }
}
