<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryRequest extends FormRequest
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
                'address_id' => 'sometimes|required|integer|exists:addresses,id',
                'price' => 'sometimes|nullable|numeric|min:0',
            ],
            default => [
                'address_id' => 'required|integer|exists:addresses,id',
                'price' => 'nullable|numeric|min:0',
            ]
        };
    }

    public function attributes(): array
    {
        return __('validation.attributes');
    }
}
