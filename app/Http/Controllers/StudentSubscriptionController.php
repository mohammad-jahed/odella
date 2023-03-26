<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

use Illuminate\Support\Facades\Gate;

class StudentSubscriptionController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function unActiveStudent(): JsonResponse
    {
        $auth = auth()->user();
        Gate::forUser($auth)->authorize('confirmRegistration');
        $students = User::role('Student')->where('status', 0)->get();
        return $this->getJsonResponse($students, "Students Fetch Successfully");
    }
}