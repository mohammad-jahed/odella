<?php

namespace App\Http\Requests\Subscription;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class UpdateSubscriptionRequest extends FormRequest
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
    #[ArrayShape(['name_ar' => "string[]", 'name_en' => "string[]", 'daysNumber' => "string[]", 'price' => "string[]"])]
    public function rules(): array
    {
        return [
            //
            'name_ar'=>['bail','string','max:255'],
            'name_en'=>['bail','string','max:255'],
            'daysNumber'=>['bail','numeric'],
            'price'=>['bail','numeric'],
        ];
    }
}
