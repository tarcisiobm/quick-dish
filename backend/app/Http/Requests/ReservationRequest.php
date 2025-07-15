<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReservationRequest extends FormRequest
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
                'table_id' => 'sometimes|integer|exists:tables,id',
                'user_id' => 'sometimes|exists:users,id',
                'reservation_date' => 'sometimes|date|after_or_equal:today',
                'start_time' => 'sometimes|date_format:H:i',
                'end_time' => 'sometimes|date_format:H:i|after:start_time',
                'guests_count' => 'sometimes|integer|min:1',
                'notes' => 'sometimes|nullable|string|max:255',
                'status' => 'sometimes|boolean',
            ],
            default => [
                'table_id' => 'required|integer|exists:tables,id',
                'user_id' => 'required|exists:users,id',
                'reservation_date' => 'required|date|after_or_equal:today',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'guests_count' => 'required|integer|min:1',
                'notes' => 'nullable|string|max:255',
                'status' => 'boolean',
            ],
        };
    }

    /**
     * Custom attribute names.
     */
    public function attributes(): array
    {
        return __('validation.attributes');
    }
}
