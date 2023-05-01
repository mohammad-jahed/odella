<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class ConfirmAttendanceRequest extends FormRequest
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
    #[ArrayShape(['confirmAttendance1' => "array", 'confirmAttendance2' => "array"])]
    public function rules(): array
    {
        return [
            //
            'confirmAttendance1'=>['boolean',Rule::in([true,false])],
            'confirmAttendance2'=>['boolean',Rule::in([true,false])],
        ];
    }
}
