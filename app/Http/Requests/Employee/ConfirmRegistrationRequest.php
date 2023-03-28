<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class ConfirmRegistrationRequest extends FormRequest
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
    #[ArrayShape(['amount' => "string[]", 'date' => "string[]"])] public function rules(): array
    {
        return [
            'amount' => ['required', 'bail', 'numeric'],
            'date' => ['required', 'bail', 'date'],
        ];
    }
}
