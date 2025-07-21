<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->method()) {
            'PUT', 'PATCH' => [
                'rating' => 'sometimes|required|integer|min:1|max:5',
                'comment' => 'sometimes|nullable|string|max:255',
                'status' => 'sometimes|boolean',
            ],
            default => [
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'nullable|string|max:255',
            ]
        };
    }

    public function attributes(): array
    {
        return __('validation.attributes');
    }
}
