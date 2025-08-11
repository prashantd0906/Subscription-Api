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
            'code' => 'required|string|unique:promo_codes,code',
            'discount' => 'required|numeric|min:0|max:100',
            'valid_till' => 'required|date|after:today'
        ];
    }
}
