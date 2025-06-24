<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProviderRequest extends FormRequest
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
            'open_from' => ['regex:/^([01]\d|2[0-3]):[0-5]\d:[0-5]\d$/'], 
            'open_to' => ['regex:/^([01]\d|2[0-3]):[0-5]\d:[0-5]\d$/'], 
            'service_id' => ['required', 'exists:service_types,id'],
            'village_id' => ['exists:villages,id'],
            'mall_id' => ['exists:malls,id'],
            'name' => ['required'],
            'description' => ['sometimes'],
            'phone' => ['required', ],
            'location' => ['required'],
            'status' => ['required'],
            'location_map' => ['required'],
            'zone_id' => ['exists:zones,id']
        ];
    }

    public function failedValidation(Validator $validator){
       throw new HttpResponseException(response()->json([
               'message'=>'validation error',
               'errors'=>$validator->errors(),
       ],400));
   }
}
