<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Models\Location;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * @throws AuthorizationException
     */
    public function update(UpdateStudentRequest $request, User $student): JsonResponse
    {
        $user = auth()->user();
        Gate::forUser($user)->authorize('updateProfile', $student);
        $credentials = $request->validated();
        if (isset($credentials['password'])) {
            $credentials['password'] = Hash::make($credentials['password']);
        }
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images/users');
            $credentials['image'] = $path;
        }

        if (isset($credentials['city_id']) || isset($credentials['area_id']) || isset($credentials['street'])) {
            /**
             * @var Location $location ;
             */
            $location = Location::query()->update($credentials);
            $credentials['location_id'] = $location->id;
        }
        $student->update($credentials);
        return $this->getJsonResponse($student, "Student Updated Successfully");

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function activeStudentsList(): JsonResponse
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();
        if ($user->can('View Student')) {
            $students = User::role('Student')->where('status', Status::Active)->get();
            return $this->getJsonResponse($students, "Students Fetch Successfully");
        } else {
            abort(Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * @throws AuthorizationException
     */
    public function unActiveStudentsList(): JsonResponse
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();
        if ($user->can('View Student')) {
            $students = User::role('Student')->where('status', Status::UnActive)->get();

            return $this->getJsonResponse($students, "Students Fetch Successfully");

        } else {
            abort(Response::HTTP_FORBIDDEN);
        }

    }
}
