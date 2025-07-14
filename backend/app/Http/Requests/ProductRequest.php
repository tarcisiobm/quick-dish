<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
        return match ($this->method()) {
            'PUT' => [
                'category_id' => 'sometimes|integer|exists:categories,id',
                'name' => 'sometimes|string|max:150',
                'description' => 'sometimes|nullable|string|max:1000',
                'price' => 'sometimes|numeric|min:0',
                'promotional_price' => 'sometimes|nullable|numeric|min:0|lt:price',
                'image' => 'sometimes|nullable|image|max:2048',
                'status' => 'sometimes|boolean',
                'ingredients' => 'sometimes|array',
                'ingredients.*.id' => 'required_with:ingredients|integer|exists:ingredients,id',
                'ingredients.*.quantity' => 'sometimes|nullable|numeric|min:0.01'
            ],
            default => [
                'category_id' => 'required|integer|exists:categories,id',
                'name' => 'required|string|max:150',
                'description' => 'sometimes|nullable|string|max:1000',
                'price' => 'required|numeric|min:0',
                'promotional_price' => 'sometimes|nullable|numeric|min:0|lt:price',
                'image' => 'sometimes|nullable|image|max:2048',
                'ingredients' => 'sometimes|array',
                'ingredients.*.id' => 'required_with:ingredients|integer|exists:ingredients,id',
                'ingredients.*.quantity' => 'sometimes|nullable|numeric|min:0.01'
            ],
        };
    }

    public function attributes(): array
    {
        return __('validation.attributes');
    }
}
