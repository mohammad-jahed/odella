<?php

namespace App\Http\Requests\Employee;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
    public function rules(): array
    {
        /**
         * @var User $user;
         * @var Subscription $subscription;
         */
        $user = $this->route('user');
        $subscription = $user->subscription;
        return [
            'day_ids' =>['required','array',"size:$subscription->daysNumber"],
            'day_ids.*' => ['required',Rule::exists('days', 'id')],
            'position_ids' =>['required','array',"size:$subscription->daysNumber"],
            'position_ids.*' => ['required',Rule::exists('transfer_positions', 'id')],
            'amount' => ['required', 'bail', 'numeric'],
            'date' => ['required', 'bail', 'date'],
        ];
    }
}
