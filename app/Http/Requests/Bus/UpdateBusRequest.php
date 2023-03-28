<?php

namespace App\Http\Requests\Bus;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class UpdateBusRequest extends FormRequest
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
    #[ArrayShape(["key" => "string[]", "capacity" => "string[]", "details" => "string[]", "image" => "string[]"])] public function rules(): array
    {
        return [
            "key" => ["string", "max:256"],
            "capacity" => ["numeric"],
            "details" => ["string", "max:256"],
            "image" => ['image', 'max:1000', 'bail'],
        ];
    }
}
