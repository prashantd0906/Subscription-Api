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
        return [
            'name' => 'required|string|max:255','unique:subscription_plans,name',
            'price' => 'required|numeric|min:0','regex:/^\d+(\.\d{1,2})?$/', //upto 2 decimal place
            'duration' => 'required|integer|min:1',
        ];
    }
}
