<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'city_id' => [Rule::exists('cities', 'id')],
            'area_id' => [Rule::exists('areas', 'id')],
            'street' => ['string', 'min:3', 'max:255'],
            'firstName' => ['bail', 'string', 'max:255'],
            'lastName' => ['bail', 'string', 'max:255'],
            'email' => ['bail', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['bail', 'string', 'min:6', 'max:256'],
            'phoneNumber' => ['bail', 'numeric', 'min:10'],
            'subscription_id' => [Rule::exists('subscriptions', 'id')],
            'transportation_line_id' => [Rule::exists('transportation_lines', 'id')],
            'transfer_position_id' => [Rule::exists('transfer_positions', 'id')],
            'image' => ['image', 'max:1000', 'bail'],
        ];
    }
}