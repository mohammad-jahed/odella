<?php

namespace App\Http\Requests\Time;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class StoreTimeRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    #[ArrayShape(['start' => "string[]", 'end' => "string[]", 'date' => "string[]"])] public function rules(): array
    {
        return [
            'start' => ['required', 'date_format:H:i', 'after:5:00'],
            'end' => ['required', 'date_format:H:i', 'before:19:00'],
            'date' => ['required', 'date'],
        ];
    }
}
