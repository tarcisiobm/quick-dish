<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->method()) {
            'PUT' => [
                'name' => 'sometimes|string|max:150',
                'description' => 'sometimes|nullable|string|max:255',
                'image' => 'sometimes|nullable|image|max:2048',
                'display_order' => 'sometimes|nullable|integer|min:1',
                'status' => 'sometimes|boolean'
            ],
            default => [
                'name' => 'required|string|max:150',
                'description' => 'sometimes|nullable|string|max:255',
                'image' => 'sometimes|nullable|image|max:2048',
                'display_order' => 'sometimes|nullable|integer|min:1'
            ],
        };
    }

    public function attributes(): array
    {
        return __('validation.attributes');
    }
}
