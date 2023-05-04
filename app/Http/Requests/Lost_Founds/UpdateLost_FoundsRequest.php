<?php

namespace App\Http\Requests\Lost_Founds;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLost_FoundsRequest extends FormRequest
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
            'image' => ['image', 'max:1000', 'bail'],
            'description' => ['string', 'min:3', 'max:255']
        ];
    }
}
