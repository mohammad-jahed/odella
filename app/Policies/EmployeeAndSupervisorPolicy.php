<?php

namespace App\Policies;

use App\Models\Program;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EmployeeAndSupervisorPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
        return $user->hasRole('Admin');
    }


    public function confirmRegistration(User $user): bool
    {
        return $user->hasRole('Employee');
    }

    public function getUnActiveStudents(User $user): bool
    {
        return $user->hasRole('Employee');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        //
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
        return $user->hasRole('Admin');

    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        //
        return $user->id == $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        //
        return $user->hasRole('Employee') || $model->hasRole('Admin');


    }

    public function confirmAttendance(User $user, Program $program): bool
    {
        /**
         * @var Program $pro ;
         */
        $programs = $user->programs;
        foreach ($programs as $pro) {
            if ($pro->id == $program->id) {
                return true;
            }
        }
        return false;
    }

    public function getStudentsInPosition(User $user, Trip $trip): bool
    {
        return $user->id == $trip->supervisor->id;
    }

}
