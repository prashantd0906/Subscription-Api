<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return [
                'name' => [
                    'required',
                    'regex:/^[a-zA-Z\s]+$/',
                    'max:255',
                    'unique:subscription_plans,name',
                ],
                'price' => [
                    'required',
                    'numeric',
                    'min:0',
                    'regex:/^\d+(\.\d{1,2})?$/',
                ],
                'duration' => [
                    'required',
                    'integer',
                    'min:1',
                ],
            ];
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                'name' => [
                    'sometimes',
                    'regex:/^[a-zA-Z\s]+$/',
                    'max:255',
                    'unique:subscription_plans,name,' . $this->route('id'),
                ],
                'price' => [
                    'sometimes',
                    'numeric',
                    'min:0',
                    'regex:/^\d+(\.\d{1,2})?$/',
                ],
                'duration' => [
                    'sometimes',
                    'integer',
                    'min:1',
                ],
            ];
        }
        return [];
    }
}
