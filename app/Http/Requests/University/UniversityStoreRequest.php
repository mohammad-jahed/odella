<?php

namespace App\Http\Requests\University;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class UniversityStoreRequest extends FormRequest
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
     * @return array
     */
    #[ArrayShape(['name_ar' => "string[]", 'name_en' => "string[]", 'shortcut' => "string[]"])]
    public function rules(): array
    {
        return [
            //
            'name_ar'=>['required','string','max:256'],
            'name_en'=>['required','string','max:256'],
            'shortcut'=>['required','string','min:3','max:4'],
        ];
    }
}
