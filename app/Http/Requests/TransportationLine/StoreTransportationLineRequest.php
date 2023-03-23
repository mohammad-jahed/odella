<?php

namespace App\Http\Requests\TransportationLine;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class StoreTransportationLineRequest extends FormRequest
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
    #[ArrayShape(['name_ar' => "string[]", 'name_en' => "string[]"])]
    public function rules(): array
    {
        return [
            'name_ar' => ['required', 'bail', 'string', 'max:255'],
            'name_en' => ['required', 'bail', 'string', 'max:255'],
        ];
    }
}
