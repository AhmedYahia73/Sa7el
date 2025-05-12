<?php

namespace App\Http\Requests\Village;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SecuirtyRequest extends FormRequest
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
        // , , , , password, image
        // email , phone, , 
        return [
            'name' => ['required'],
            'location' => ['required'],
            'shift_from' => ['required', 'regex:/^([01]\d|2[0-3]):[0-5]\d:[0-5]\d$/'],
            'shift_to' => ['required', 'regex:/^([01]\d|2[0-3]):[0-5]\d:[0-5]\d$/'],
            'email' => ['required', 'email'],
            'phone' => ['required'],
            'type' => ['required', 'in:pool,gate,beach'],
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
