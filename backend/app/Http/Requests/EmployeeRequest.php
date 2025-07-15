<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
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
            'PUT', 'PATCH' => [
                'job_title' => 'sometimes|required|string|max:150',
                'salary' => 'sometimes|required|numeric|min:0',
                'hire_date' => 'sometimes|required|date',
                'termination_date' => 'sometimes|nullable|date|after_or_equal:hire_date',
                'work_schedule' => 'sometimes|required|string|max:255',
            ],
            default => [
                'user_id' => 'required|exists:users,id|unique:employees,user_id',
                'job_title' => 'required|string|max:150',
                'salary' => 'required|numeric|min:0',
                'hire_date' => 'nullable|date',
                'termination_date' => 'nullable|date|after_or_equal:hire_date',
                'work_schedule' => 'required|string|max:255',
            ]
        };
    }

    /**
     * Custom attribute names for validation errors.
     */
    public function attributes()
    {
        return __('validation.attributes');
    }
}
