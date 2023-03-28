<?php

namespace App\Http\Requests\Driver;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class UpdateDriverRequest extends FormRequest
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
            "firstname" => ['string', 'max:255'],
            "lastname" => ['string', 'max:255'],
            "number" => ['numeric', 'min:10'],
        ];
    }
}
