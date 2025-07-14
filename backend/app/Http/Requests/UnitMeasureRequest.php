<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnitMeasureRequest extends FormRequest
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
                'abbreviation' => 'sometimes|nullable|string|max:15'
            ],
            default => [
                'name' => 'required|string|max:150',
                'abbreviation' => 'sometimes|nullable|string|max:15'
            ]
        };
    }
    public function attributes(): array
    {
        return __('validation.attributes');
    }
}
