<?php

namespace App\Http\Requests\City;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class UpdateCityRequest extends FormRequest
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
    #[ArrayShape(["name_ar" => "string[]", "name_en" => "string[]"])]
    public function rules(): array
    {
        return [
            //
            "name_ar"=>["bail","string","max:256"],
            "name_en"=>["bail","string","max:256"],
        ];
    }
}
