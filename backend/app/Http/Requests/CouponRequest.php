<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $couponId = $this->route('coupon');

        return match ($this->method()) {
            'PUT' => [
                'code' => 'sometimes|string|max:50|unique:coupons,code,' . $couponId,
                'description' => 'sometimes|nullable|string|max:255',
                'discount_type' => 'sometimes|in:percentage,fixed',
                'discount_value' => 'sometimes|numeric|min:0',
                'min_order_value' => 'sometimes|numeric|min:0',
                'usage_limit' => 'sometimes|nullable|integer|min:1',
                'used_count' => 'sometimes|integer|min:0',
                'start_date' => 'sometimes|nullable|date',
                'end_date' => 'sometimes|nullable|date|after_or_equal:start_date',
                'status' => 'sometimes|boolean',
            ],
            default => [
                'code' => 'required|string|max:50|unique:coupons,code',
                'description' => 'nullable|string|max:255',
                'discount_type' => 'required|in:percentage,fixed',
                'discount_value' => 'required|numeric|min:0',
                'min_order_value' => 'required|numeric|min:0',
                'usage_limit' => 'nullable|integer|min:1',
                'used_count' => 'sometimes|integer|min:0',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'status' => 'sometimes|boolean',
            ]
        };
    }

    public function attributes(): array
    {
        return __('validation.attributes');
    }
}
