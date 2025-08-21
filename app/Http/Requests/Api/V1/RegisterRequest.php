<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'regex:/^[a-zA-Z\s]+$/', 'max:255'],
            'email' => [
                'required',
                'string',
                'email:rfc,dns', 
                'max:255',
                'unique:users,email',
                'regex:/^[a-zA-Z][\w.-]*@[a-zA-Z][\w.-]*\.[a-zA-Z]{2,}$/'
            ],
            'password' => [
                'required',          
                'string',           
                'min:6',             
                'confirmed',        
                'not_regex:/^\s*$/'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'password.not_regex' => 'The password cannot be empty or just spaces.',
            'email.unique' => 'You have already registered with this email. Please login.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            \App\Helpers\ApiResponse::error($validator->errors(), 422)
        );
    }
}
