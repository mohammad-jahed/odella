<?php

namespace App\Http\Requests\TransferPosition;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransferPositionRequest extends FormRequest
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

    public function rules(): array
    {
        return [
            'line_id'=>['required',Rule::exists('transportation_lines','id')],
            'name_ar' => ['required', 'bail', 'string', 'max:255'],
            'name_en' => ['required', 'bail', 'string', 'max:255'],
            'lng'=>['required','numeric'],
            'lat'=>['required','numeric'],
        ];
    }
}
