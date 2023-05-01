<?php

namespace App\Http\Requests\Claims;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClaimStoreRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            //
            'trip_id' => ['required', Rule::exists('trips', 'id')],
            'description' => ['required', 'bail', 'string']
        ];
    }
}
