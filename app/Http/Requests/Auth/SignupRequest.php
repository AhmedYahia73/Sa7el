<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class SignupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [
            'name' => ['required'],  
            'email' => ['required', 'email',
            Rule::unique('users')->where(function ($query) {
                return $query->whereIn('role', ['village', 'maintenance_provider', 'provider']);
            })],
            'phone' => [
            Rule::unique('users')->where(function ($query) {
                return $query->whereIn('role', ['village', 'maintenance_provider', 'provider']);
            })],
            'password' => ['required'],
            'parent_user_id' => ['nullable', 'exists:users,id'],
            'gender' => ['in:male,female'], 
            'birthDate' => ['date'],
        ];
    }

    public function failedValidation(Validator $validator){
        $firstError = $validator->errors()->first();
       throw new HttpResponseException(response()->json([
            'errors'=> $firstError,
       ],400));
   }
}
