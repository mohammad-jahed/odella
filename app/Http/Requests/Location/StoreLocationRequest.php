<?php

namespace App\Http\Requests\Location;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class StoreLocationRequest extends FormRequest
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
    #[ArrayShape(["city_id" => "array", "area_id" => "array", "street" => "string[]"])]
    public function rules(): array
    {
        return [
            //
            "city_id" => ['required', Rule::exists("cities", "id")],
            "area_id" => ['required', Rule::exists("areas", "id")],
            "street" => ['required', 'string', 'min:3', 'max:255']
        ];
    }
}
