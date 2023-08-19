<?php

namespace App\Http\Requests\Algorithm;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AlgorithmInputRequest extends FormRequest
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
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        /**
         * @var User $auth ;
         */
        $auth = auth()->user();
        $subscription_days = $auth->subscription->daysNumber;
        return [
            //
            'goTimes' => ['required', 'array', "size:$subscription_days"],
            'goTimes.*' => ['required', 'date_format:H:i'],
            'returnTimes' => ['required', 'array', "size:$subscription_days"],
            'returnTimes.*' => ['required', 'date_format:H:i'],
            'day_ids' =>['required','array',"size:$subscription_days"],
            'day_ids.*' => ['required',Rule::exists('days', 'id')],
        ];
    }
}
