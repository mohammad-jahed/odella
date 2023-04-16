<?php

namespace App\Http\Requests\Trip;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class UpdateTripRequest extends FormRequest
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
    #[ArrayShape(['supervisor_id' => "array", 'bus_driver_id' => "array", 'line_id' => "array", 'start' => "string[]", 'date' => "string[]", 'position_ids' => "string[]", 'position_ids.*' => "array", 'time' => "string[]", 'time.*' => "string[]"])]
    public function rules(): array
    {
        return [
            'supervisor_id' => [Rule::exists('users', 'id')],
            'bus_driver_id' => [Rule::exists('bus_drivers', 'id')],
            'line_id' => [Rule::exists('transportation_lines', 'id')],
            'start' => ['string', 'min:3', 'max:255'],
            'date' => ['bail', 'string', 'max:255'],
            'position_ids' => ['array'],
            'position_ids.*' => [Rule::exists('transfer_positions', 'id')],
            'time' => ['array'],
            'time.*' => ['date_format:H:i'],
        ];
    }
}
