<?php

namespace App\Http\Requests\Guest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class DailyReservationRequest extends FormRequest
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
     * @return array
     */
    #[ArrayShape(['name' => "string[]", 'phoneNumber' => "string[]", 'transfer_position_id' => "array", 'seatsNumber' => "string[]", 'fcm_token' => "string[]"])]
    public function rules(): array
    {
        return [
            //
            'name' => ['required', 'bail', 'string', 'max:255'],
            'phoneNumber' => ['required', 'bail', 'numeric', 'min:10'],
            'transfer_position_id' => ['required', Rule::exists('transfer_positions', 'id')],
            'seatsNumber' => ['required', 'bail', 'numeric', 'min:1', 'max:3'],
            'fcm_token' => ['required', 'string']
        ];
    }
}
