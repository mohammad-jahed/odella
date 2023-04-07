<?php

namespace App\Http\Requests\Trip;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTripRequest extends FormRequest
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
     */

    public function rules(): array
    {
        return [
            'supervisor_id' => ['required', Rule::exists('users', 'id')],
            'bus_driver_id' => ['required', Rule::exists('bus_drivers', 'id')],
            'line_id' => ['required', Rule::exists('transportation_lines', 'id')],
            'start' => ['required', 'string', 'min:3', 'max:255'],
            'date' => ['required', 'bail', 'string', 'max:255'],
            'status' => ['required', 'integer','between:1,2'],
            'position_ids' => ['required', 'array'],
            'position_ids.*' => ['required', Rule::exists('transfer_positions', 'id')],
            'time' => ['required', 'array'],
            'time.*' => ['required', 'date_format:H:i'],
        ];
    }
}
