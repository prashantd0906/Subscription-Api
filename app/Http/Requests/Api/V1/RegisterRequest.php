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
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email:rfc,dns', 
                'max:255',
                'unique:users,email',
                'regex:/^[a-zA-Z][\w.-]*@[a-zA-Z][\w.-]*\.[a-zA-Z]{2,}$/'
            ],
            'password' => [
                'required',          // must be present
                'string',            // must be a string
                'min:6',             // at least 6 characters
                'confirmed',         // must match password_confirmation
                'not_regex:/^\s*$/'  // cannot be empty or spaces only
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'password.not_regex' => 'The password cannot be empty or just spaces.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            \App\Helpers\ApiResponse::error($validator->errors(), 422)
        );
    }
}
