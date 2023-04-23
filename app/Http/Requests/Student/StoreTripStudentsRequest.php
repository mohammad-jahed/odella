<?php

namespace App\Http\Requests\Student;

use App\Models\Trip;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class StoreTripStudentsRequest extends FormRequest
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
    #[ArrayShape(['student_ids' => "string[]", 'student_ids.*' => "array"])]
    public function rules(): array
    {
        /**
         * @var Trip $trip ;
         */
        $trip = $this->route('trip');
        $busCapacity = $trip->busDriver->bus->capacity;
        $arraySize = $busCapacity - $trip->users()->count();

        return [
            //
            'student_ids' => ['required', 'array', "max:$arraySize"],
            'student_ids.*' => ['required', 'bail', 'min:1', Rule::exists('users', 'id')]
        ];
    }
}
