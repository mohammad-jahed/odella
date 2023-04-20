<?php

namespace App\Policies;

use App\Models\Location;
use App\Models\User;

class LocationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Location $location): bool
    {
        //
        /**
         * @var User $currentUser ;
         * @var User[] $users;
         */
//        $users = $location->users;
//        foreach ($users as $currentUser) {
//            if ($user->id == $currentUser->id) {
//                return true;
//            }
//        }
//        return false;
        return true;
    }


    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Location $location): bool
    {
        /**
         * @var User $currentUser ;
         * @var User[] $users;
         */
        $users = $location->users;
        foreach ($users as $currentUser) {
            if ($user->id == $currentUser->id) {
                return true;
            }
        }
        return false;

    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Location $location): bool
    {
        //
        /**
         * @var User $currentUser ;
         * @var User[] $users;
         */
        $users = $location->users;
        foreach ($users as $currentUser) {
            if ($user->id == $currentUser->id) {
                return true;
            }
        }
        return false;
    }

}
