<?php

namespace App\Policies;

use App\Models\Claim;
use App\Models\User;

class ClaimPolicy
{


    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Claim $claim): bool
    {
        //
        return
            $user->id === $claim->user->id ||
            $user->id === $claim->trip->supervisor->id ||
            $user->hasRole('Admin') ||
            $user->hasRole('Employee');
    }


    public function update(User $user, Claim $claim): bool
    {
        //
        return $user->id === $claim->user->id;

    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Claim $claim): bool
    {
        //
        return $user->id === $claim->user->id;
    }


}
