<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class UpdateEmployeeRequest extends FormRequest
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
    #[ArrayShape(['city_id' => "array", 'area_id' => "array", 'street' => "string[]", 'firstName' => "string[]", 'lastName' => "string[]", 'oldPassword' => "array", 'newPassword' => "string[]", 'phoneNumber' => "string[]", 'image' => "string[]"])]
    public function rules(): array
    {
        $user = auth()->user();
        return [
            //
            'city_id' => [Rule::exists('cities', 'id')],
            'area_id' => [Rule::exists('areas', 'id')],
            'street' => ['string', 'min:3', 'max:255'],
            'firstName' => ['bail', 'string', 'max:255'],
            'lastName' => ['bail', 'string', 'max:255'],
            'oldPassword' => [
                'bail',
                'string',
                'min:6',
                'max:256',
                function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->getAuthPassword())) {
                        $fail('Your password was not updated, since the provided current password does not match.');
                    }
                }
            ],
            'newPassword' => ['bail', 'string', 'min:6', 'max:256', 'confirmed'],
            'phoneNumber' => ['bail', 'numeric', 'min:10'],
            'image' => ['image', 'bail'],
        ];
    }
}
