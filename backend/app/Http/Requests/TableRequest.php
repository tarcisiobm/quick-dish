<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TableRequest extends FormRequest
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
        $tableId = $this->route('table');

        return match ($this->method()) {
            'PUT' => [
                'number' => 'sometimes|numeric|min:0|unique:tables,number,' . $tableId,
                'capacity' => 'sometimes|numeric|min:0',
                'status' => 'sometimes|boolean',
            ],
            default => [
                'number' => 'required|numeric|min:0|unique:tables,number',
                'capacity' => 'required|numeric|min:0',
                'status' => 'sometimes|boolean',
            ]
        };
    }

    public function attributes(): array
    {
        return __('validation.attributes');
    }
}
