<?php

namespace App\Http\Requests\Coupon;

use Illuminate\Foundation\Http\FormRequest;

class CalculatePriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'course_id'   => 'required|exists:courses,id',
            'coupon_code' => 'nullable|string|exists:coupons,code',
        ];
    }
}
