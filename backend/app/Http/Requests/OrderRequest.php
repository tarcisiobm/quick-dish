<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->method()) {
            'PUT', 'PATCH' => [
                'status' => ['required', Rule::in(['pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled'])],
                'employee_id' => 'sometimes|nullable|integer|exists:users,id',
            ],
            default => [
                'user_id' => 'required|integer|exists:users,id',
                'order_type' => ['required', Rule::in(['dine-in', 'delivery', 'takeout'])],
                'delivery_id' => 'required_if:order_type,delivery|integer|exists:deliveries,id',
                'table_id' => 'required_if:order_type,dine-in|integer|exists:tables,id',
                'notes' => 'nullable|string|max:1000',
                'coupon_code' => 'nullable|string|exists:coupons,code',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|integer|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.notes' => 'nullable|string|max:500',
                'items.*.additionals' => 'nullable|array',
                'items.*.additionals.*.ingredient_id' => [
                    'required',
                    'integer',
                    Rule::exists('ingredients', 'id')->where(function ($query) {
                        $query->where('is_additional', true);
                    }),
                ],
                'items.*.additionals.*.quantity' => 'required|integer|min:1',
            ]
        };
    }

    public function attributes(): array
    {
        return __('validation.attributes');
    }
}
