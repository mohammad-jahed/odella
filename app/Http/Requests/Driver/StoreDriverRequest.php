<?php

namespace App\Http\Requests\Driver;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class StoreDriverRequest extends FormRequest
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
     #[ArrayShape(['bus_ids' => "string[]", 'bus_ids.*' => "array", "firstname" => "string[]", "lastname" => "string[]", "number" => "string[]"])]
     public function rules(): array
    {
        return [
            'bus_ids' => ['required', 'array'],
            'bus_ids.*' => ['required',Rule::exists('buses', 'id')],
            "firstname" => ['required', 'bail', 'string', 'max:255'],
            "lastname" => ['required', 'bail', 'string', 'max:255'],
            "number" => ['required', 'bail', 'numeric', 'min:10'],
        ];
    }
}
