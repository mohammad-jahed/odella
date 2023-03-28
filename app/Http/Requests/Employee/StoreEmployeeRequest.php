<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class StoreEmployeeRequest extends FormRequest
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
    #[ArrayShape(['city_id' => "array", 'area_id' => "array", 'street' => "string[]", 'firstName' => "string[]", 'lastName' => "string[]", 'email' => "string[]", 'password' => "string[]", 'phoneNumber' => "string[]", 'image' => "string[]"])] public function rules(): array
    {
        return [
            'city_id' => ['required', Rule::exists('cities', 'id')],
            'area_id' => ['required', Rule::exists('areas', 'id')],
            'street' => ['required', 'string', 'min:3', 'max:255'],
            'firstName' => ['required', 'bail', 'string', 'max:255'],
            'lastName' => ['required', 'bail', 'string', 'max:255'],
            'email' => ['required', 'bail', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'bail', 'string', 'min:6', 'max:256'],
            'phoneNumber' => ['required', 'bail', 'numeric', 'min:10'],
            'image' => ['image', 'max:1000', 'bail'],
        ];
    }
}
