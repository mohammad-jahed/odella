<?php

namespace App\Http\Requests\Driver;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class StorDriverRequest extends FormRequest
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
     #[ArrayShape(["firstname" => "string[]", "lastname" => "string[]", "number" => "string[]"])] public function rules(): array
    {
        return [
            "firstname" => ['required', 'bail', 'string', 'max:255'],
            "lastname" => ['required', 'bail', 'string', 'max:255'],
            "number" => ['required', 'bail', 'numeric', 'min:10'],
        ];
    }
}
