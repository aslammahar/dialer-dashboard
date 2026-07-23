<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ViciDialStatsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'stage' => ['sometimes', 'in:csv,json'],
            'dialer' => ['sometimes', 'in:1,3,4'],
            'group_by_campaign' => ['sometimes', 'in:YES,NO'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.required' => 'Start date is required.',
            'end_date.required' => 'End date is required.',
            'end_date.after_or_equal' => 'End date must be the same or after start date.',
            'dialer.in' => 'Please select a valid dialer (1, 3, or 4).',
        ];
    }
}
