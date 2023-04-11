<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class ResetPasswordRequest extends FormRequest
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
    #[ArrayShape(['code' => "string[]", 'email' => "array", 'newPassword' => "string[]"])] public function rules(): array
    {
        return [
            'code' => ['required'],
            'email' => ['required', 'string', 'email', Rule::exists('users', 'email')],
            'newPassword'=>['required','min:8'],
        ];
    }
}
