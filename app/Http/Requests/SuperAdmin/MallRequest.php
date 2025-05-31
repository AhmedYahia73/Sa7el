<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class MallRequest extends FormRequest
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
            'name' => ['required'],
            'description' => ['sometimes'], 
            'open_from' => ['regex:/^([01]\d|2[0-3]):[0-5]\d:[0-5]\d$/'], 
            'open_to' => ['regex:/^([01]\d|2[0-3]):[0-5]\d:[0-5]\d$/'],  
            'zone_id' => ['required', 'exists:zones,id'], 
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
