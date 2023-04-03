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
    public function index(): JsonResponse
    {
        //
        /**
         * @var User $user ;
         */
        $user = auth()->user();
        if ($user->can('View Student')) {
            $students = User::role('Student')->get();
            return $this->getJsonResponse($students, "Students Fetched Successfully");
        } else {
            abort(Response::HTTP_FORBIDDEN);
        }

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
    public function show(User $student): JsonResponse
    {
        //
        /**
         * @var User $user ;
         */
        $user = auth()->user();
        if ($user->can('View Student')) {
            return $this->getJsonResponse($student, "Student Fetched Successfully");
        } else {
            abort(Response::HTTP_FORBIDDEN);
        }
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

        if (isset($credentials['newPassword'])) {
            $credentials['password'] = Hash::make($credentials['newPassword']);
        }
        if ($request->hasFile('image')) {

            $path = $request->file('image')->store('images/users');

            $credentials['image'] = $path;
        }
        /**
         * @var Location $location ;
         */
        $data = [];
        if (isset($credentials['city_id'])) {
            $data += ['city_id' => $credentials['city_id']];
        }
        if (isset($credentials['area_id'])) {
            $data += ['area_id' => $credentials['area_id']];
        }
        if (isset($credentials['street'])) {
            $data += ['street' => $credentials['street']];
        }
        $location = $student->location;

        $location->update($data);

        $student->update($credentials);

        return $this->getJsonResponse($student, "Student Updated Successfully");

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $student): JsonResponse
    {
        //
        /**
         * @var User $user ;
         */
        $user = auth()->user();
        if ($user->can('Delete Student')) {
            $student->delete();
            return $this->getJsonResponse([], 'Student Deleted Successfully');
        } else {
            abort(Response::HTTP_FORBIDDEN);
        }

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
