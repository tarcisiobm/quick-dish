<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
                'user_id' => 'required',
                'exists:users,id',
                'zipcode' => 'sometimes|string|max:10',
                'street' => 'sometimes|string|max:255',
                'number' => 'sometimes|numeric|min:0',
                'city' => 'sometimes|string|max:100',
                'state' => 'sometimes|string|max:50',
                'refference' => 'sometimes|nullable|string|max:255',
                'complement' => 'sometimes|nullable|string|max:255',
                'status' => 'sometimes|boolean',
                'is_default' => 'sometimes|boolean'
            ],
            default => [
                'user_id' => 'required','exists:users,id',
                'zipcode' => 'required|string|max:10',
                'street' => 'required|string|max:255',
                'number' => 'required|numeric|min:0',
                'city' => 'required|string|max:100',
                'state' => 'required|string|max:50',
                'refference' => 'sometimes|nullable|string|max:255',
                'complement' => 'sometimes|nullable|string|max:255',
                'status' => 'sometimes|boolean',
                'is_default' => 'sometimes|boolean'
            ]
        };
    }

    public function attributes(): array
    {

        return __('validation.attributes');
    }
}
