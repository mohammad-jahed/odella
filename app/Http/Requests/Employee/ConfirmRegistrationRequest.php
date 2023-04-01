<?php

namespace App\Http\Requests\Employee;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class ConfirmRegistrationRequest extends FormRequest
{


    public function authorize(): bool
    {
        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     */
    #[ArrayShape(['day_ids' => "string[]", 'day_ids.*' => "array", 'position_ids' => "string[]", 'position_ids.*' => "array", 'trip_ids' => "string[]", 'trip_ids.*' => "array", 'amount' => "string[]", 'date' => "string[]"])]
    public function rules(): array
    {
        /**
         * @var User $user;
         * @var Subscription $subscription;
         */
        $user = $this->route('user');
        $subscription = $user->subscription;
        $tripsNumber = $subscription->daysNumber * 2;
        return [
            'day_ids' =>['required','array',"size:$subscription->daysNumber"],
            'day_ids.*' => ['required',Rule::exists('days', 'id')],
            'position_ids' =>['required','array',"size:$subscription->daysNumber"],
            'position_ids.*' => ['required',Rule::exists('transfer_positions', 'id')],
            'trip_ids' => ['required','array',"size:$tripsNumber"],
            'trip_ids.*' => ['required',Rule::exists('trips', 'id')],
            'amount' => ['required', 'bail', 'numeric'],
            'date' => ['required', 'bail', 'date'],
        ];
    }
}
