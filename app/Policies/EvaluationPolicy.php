<?php

namespace App\Policies;

use App\Models\User;
use App\Models\evaluation;

class EvaluationPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Evaluation $evaluation): bool
    {
        //
        return
            $user->id == $evaluation->user->id ||
            $user->id == $evaluation->trip->supervisor->id ||
            $user->hasRole('Admin');
    }


    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, evaluation $evaluation): bool
    {
        //
        return $user->id == $evaluation->user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, evaluation $evaluation): bool
    {
        //
        return $user->id == $evaluation->user->id;

    }


}
