<?php

namespace App\Http\Requests\Lost_Founds;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLost_FoundsRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'trip_id' => ['required', Rule::exists('trips', 'id')],
            'image' => ['image', 'max:1000', 'bail'],
            'description' => ['required', 'string', 'min:3', 'max:255']
        ];
    }
}
