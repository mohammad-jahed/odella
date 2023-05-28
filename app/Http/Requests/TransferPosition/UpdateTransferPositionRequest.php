<?php

namespace App\Http\Requests\TransferPosition;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTransferPositionRequest extends FormRequest
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
            'line_id'=>[Rule::exists('transportation_lines','id')],
            'name_ar' => ['bail', 'string', 'max:255'],
            'name_en' => ['bail', 'string', 'max:255'],
            'lng'=>['numeric'],
            'lat'=>['numeric'],
        ];
    }
}
