<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Http\Requests\Student\ConfirmAttendanceRequest;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Http\Resources\UserResource;
use App\Models\Location;
use App\Models\Program;
use App\Models\TransferPosition;
use App\Models\Trip;
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
        /**
         * @var User $user ;
         */
        $user = auth()->user();

//        if ($user->can('View Student')) {

            $students = User::role('Student')->paginate(10);

            if ($students->isEmpty()) {

                return $this->getJsonResponse(null, "There Are No Students Found!");
            }

            return $this->getJsonResponse($students, "Students Fetched Successfully");

//        } else {
//            abort(Response::HTTP_UNAUTHORIZED
//                , "Unauthorized , You Dont Have Permission To Access This Action");
//        }

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
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('View Student')) {

            return $this->getJsonResponse($student, "Student Fetched Successfully");

        } else {
            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
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
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('Delete Student')) {

            $student->delete();

            return $this->getJsonResponse(null, 'Student Deleted Successfully');

        } else {
            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
        }

    }

    public function activeStudentsList(): JsonResponse
    {
        /**
         * @var User $user ;
         */
//        $user = auth()->user();
//
//        if ($user->can('View Student')) {

            $students = User::role('Student')->where('status', Status::Active)->paginate(10);

            if ($students->isEmpty()) {

                return $this->getJsonResponse(null, "There Are No Active Students Found!");
            }

            $students->load(['location', 'subscription', 'line',
                'position', 'university', 'payments', 'programs']);

            $activeStudents = UserResource::collection($students)->response()->getData(true);

            return $this->getJsonResponse($activeStudents, "Students Fetch Successfully");

//        } else {
//
//            abort(Response::HTTP_UNAUTHORIZED
//                , "Unauthorized , You Dont Have Permission To Access This Action");
//        }
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

//        if ($user->can('View Student')) {

            $students = User::role('Student')->where('status', Status::UnActive)->paginate(10);

            if ($students->isEmpty()) {

                return $this->getJsonResponse(null, "There Are No UnActive Students Found!");
            }

            $students->load(['location', 'subscription', 'line', 'position', 'university']);

            $unActiveStudents = UserResource::collection($students)->response()->getData(true);

            return $this->getJsonResponse($unActiveStudents, "Students Fetch Successfully");

//        } else {
//
//            abort(Response::HTTP_UNAUTHORIZED
//                , "Unauthorized , You Dont Have Permission To Access This Action");
//        }

    }

    /**
     * @throws AuthorizationException
     */
    public function confirmAttendance(Program $program, ConfirmAttendanceRequest $request): JsonResponse
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        Gate::forUser($user)->authorize('confirmAttendance', $program);

        $data = $request->validated();

        $program->confirmAttendance1 = $data['confirmAttendance1'];
        $program->confirmAttendance2 = $data['confirmAttendance2'];

        return $this->getJsonResponse($program, "Your Attendance Is Confirmed Successfully");
    }

    /**
     * @throws AuthorizationException
     */
    public function getAllStudentsInThePosition(Trip $trip, TransferPosition $position): JsonResponse
    {
        /**
         * @var User $student ;
         * @var User $auth ;
         */
        $auth = auth()->user();

        Gate::forUser($auth)->authorize('getStudentsInPosition', $trip);

        $students = $trip->users;

        $users = [];

        foreach ($students as $student) {

            $users += [
                'students' => $student->whereHas(
                    "programs",
                    function ($query) use ($position) {
                        $query
                            ->where("transfer_position_id", $position->id)
                            ->where('confirmAttendance1', true);
                    }
                )->get()
            ];
        }

        $users += ['studentsNumber' => sizeof($users) + 1];

        return $this->getJsonResponse($users, "Students Fetched Successfully");
    }
}
