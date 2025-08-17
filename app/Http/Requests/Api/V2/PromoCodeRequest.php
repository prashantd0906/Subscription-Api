<?php

namespace App\Http\Requests\Api\V2;

use Illuminate\Foundation\Http\FormRequest;

class PromoCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'promo_code' => 'required|string|unique:promo_codes,code|max:20|regex:/^[A-Z0-9_-]+$/i',
            'discount'   => 'required|numeric|min:0|max:100',
            'valid_till' => 'required|date|after:today',
        ];
    }
}
