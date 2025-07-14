<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IngredientRequest extends FormRequest
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
                'name' => 'sometimes|string|max:150',
                'description' => 'sometimes|string',
                'unit_price' => 'sometimes|numeric|min:0',
                'quantity' => 'sometimes|numeric|min:0',
                'min_quantity' => 'sometimes|numeric|min:0',
                'max_quantity' => 'sometimes|numeric|min:0',
                'supplier_id' => 'sometimes|exists:suppliers,id',
                'unit_measure_id' => 'sometimes|exists:unit_measures,id',
                'is_additional' => 'sometimes|boolean',
                'status' => 'sometimes|boolean'
            ],
            default => [
                'name' => 'required|string|max:150',
                'description' => 'sometimes|nullable|string',
                'unit_price' => 'required|numeric|min:0',
                'quantity' => 'required|numeric|min:0',
                'min_quantity' => 'required|numeric|min:0',
                'max_quantity' => 'sometimes|nullable|numeric|min:0',
                'supplier_id' => 'required|exists:suppliers,id',
                'unit_measure_id' => 'required|exists:unit_measures,id',
                'is_additional' => 'sometimes|boolean',
                'status' => 'sometimes|boolean'
            ]
        };
    }
    public function attributes(): array
    {
        return __('validation.attributes');
    }
}
