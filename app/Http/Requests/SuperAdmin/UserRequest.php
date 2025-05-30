<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
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
        if ($this->input('user_type') == 'rent') {
            return [
                'name' => ['required'],
                'email' => ['required', 'email'],
                'phone' => ['required'],
                'status' => ['required', 'boolean'],
                'parent_user_id' => ['nullable', 'exists:users,id'],
                'gender' => ['required', 'in:male,female'], 
                'birthDate' => ['required', 'date'],
            ];
        }
        else{
            return [
                'name' => ['required'],
                'user_type' => ['required', 'in:owner,rent,visitor'], 
                'email' => ['required', 'email'],
                'phone' => ['required'], 
                'status' => ['required', 'boolean'],
                'parent_user_id' => ['nullable', 'exists:users,id'],
                'gender' => ['required', 'in:male,female'], 
                'birthDate' => ['required', 'date'],
            ];
        }
    }

    public function failedValidation(Validator $validator){
       throw new HttpResponseException(response()->json([
               'message'=>'validation error',
               'errors'=>$validator->errors(),
       ],400));
   }
}
