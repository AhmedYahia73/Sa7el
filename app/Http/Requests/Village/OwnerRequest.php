<?php

namespace App\Http\Requests\Village;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class OwnerRequest extends FormRequest
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
            'gender' => ['in:male,female'],
            'birthDate' => ['date'],
            'email' => ['required', 'email'],
            'phone' => ['sometimes'],
            'parent_user_id' => ['exists:users,id'],
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
