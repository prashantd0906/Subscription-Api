<?php

namespace App\Http\Requests\Api\V2;

use Illuminate\Foundation\Http\FormRequest;

class AssignPromoCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Admin middleware already handles role check
    }

    public function rules(): array
    {
        return [
            'subscription_id' => 'required|exists:subscriptions,id',
            'promo_code_id' => 'required|exists:promo_codes,id',
        ];
    }
}
