<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('id');

        return match ($this->method()) {
            'PUT', 'PATCH' => [
                'name' => "sometimes|string|max:150|unique:categories,name,{$categoryId}",
                'description' => 'sometimes|nullable|string|max:255',
                'image' => 'sometimes|nullable|string',
                'display_order' => 'sometimes|nullable|integer|min:1',
                'status' => 'sometimes|boolean',
            ],
            default => [
                'name' => 'required|string|max:150|unique:categories,name',
                'description' => 'sometimes|nullable|string|max:255',
                'image' => 'sometimes|nullable|string',
                'display_order' => 'sometimes|nullable|integer|min:1',
            ],
        };
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da categoria é obrigatório.',
            'name.unique' => 'Já existe uma categoria com este nome.',
            'name.max' => 'O nome não pode exceder 150 caracteres.',
            'description.max' => 'A descrição não pode exceder 255 caracteres.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Category name',
            'display_order' => 'Exibition order',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        parent::failedValidation($validator);
    }
}
