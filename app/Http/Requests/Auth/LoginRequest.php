<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @property mixed $email
 * @property mixed $password
 */
class LoginRequest extends FormRequest
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
    #[ArrayShape(['email' => "string[]", 'password' => "string[]", 'fcm_token' => "string[]"])]
    public function rules(): array
    {
        return [
            //
            'email' => ['required', 'bail', 'string', 'email'],
            'password' => ['required', 'bail', 'string', 'min:6', 'max:256'],
            'fcm_token'=>['string']
        ];
    }
}
