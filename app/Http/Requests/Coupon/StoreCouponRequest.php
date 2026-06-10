<?php

namespace App\Http\Requests\Coupon;

use Illuminate\Foundation\Http\FormRequest;

class StoreCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code'         => 'required|string|unique:coupons,code|max:50',
            'type'         => 'required|string|in:percentage,fixed',
            'value'        => 'required|numeric|min:0',
            'usage_limit'  => 'nullable|integer|min:1',
            'expires_at'   => 'nullable|date|after:now',
        ];
    }
}
