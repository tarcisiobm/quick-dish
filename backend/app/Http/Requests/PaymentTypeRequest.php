<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentTypeRequest extends FormRequest
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
                'status' => 'sometimes|boolean'
            ],
            default => [
                'name' => 'required|string|max:150',
                'status' => 'sometimes|boolean'
            ]
        };
    }

    public function attributes(): array
    {
        return __('validation.attributes');
    }
}
