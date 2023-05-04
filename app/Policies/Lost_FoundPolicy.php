<?php

namespace App\Policies;

use App\Models\Lost_Found;
use App\Models\User;

class Lost_FoundPolicy
{

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Lost_Found $lostFound): bool
    {
        //
        return
            $user->id === $lostFound->user->id ||
            $user->id === $lostFound->trip->supervisor->id ||
            $user->hasRole('Admin') ||
            $user->hasRole('Employee');
    }


    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Lost_Found $lostFound): bool
    {
        //
        return $user->id === $lostFound->user->id;

    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Lost_Found $lostFound): bool
    {
        //
        return $user->id === $lostFound->user->id;

    }


}
