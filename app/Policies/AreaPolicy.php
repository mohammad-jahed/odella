<?php

namespace App\Policies;

use App\Models\User;


class AreaPolicy
{

    public function create(User $user): bool
    {
        //
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        //
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        //
        return $user->hasRole('Admin');
    }

}
