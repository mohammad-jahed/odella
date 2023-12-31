<?php

namespace App\Http\Requests\Area;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class StoreAreaRequest extends FormRequest
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
    #[ArrayShape(["city_id" => "array", "name_ar" => "string[]", "name_en" => "string[]"])]
    public function rules(): array
    {
        return [
            //
            "city_id" => ["required", Rule::exists('cities', 'id')],
            "name_ar" => ['required', "bail", "string", "max:256"],
            "name_en" => ['required', "bail", "string", "max:256"],
        ];
    }
}
