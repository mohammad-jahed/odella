<?php

namespace App\Http\Controllers;

use App\Enums\Messages;
use App\Enums\Status;
use App\Http\Requests\Student\ConfirmAttendanceRequest;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Http\Resources\UserResource;
use App\Models\Program;
use App\Models\TransferPosition;
use App\Models\Trip;
use App\Models\User;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        if ($user->can('View Student')) {

            $students = User::role('Student')
                ->with(['subscription', 'line', 'position', 'university', 'location', 'pays'])
                ->paginate(10);

            if ($students->isEmpty()) {

                return $this->getJsonResponse(null, "There Are No Students Found!");
            }

            $students = UserResource::collection($students)->response()->getData(true);

            return $this->getJsonResponse($students, "Students Fetched Successfully");

        } else {
            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
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
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('View Student')) {

            $student->load(['subscription', 'line', 'position', 'university', 'location', 'pays']);

            $student = new UserResource($student);

            return $this->getJsonResponse($student, "Student Fetched Successfully");

        } else {
            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
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

        try {

            $credentials = $request->validated();

            if ($request->hasFile('image')) {

                $path = $request->file('image')->store('images/users');

                $credentials['image'] = $path;
            }

            if (isset($credentials['newPassword'])) {

                $credentials['password'] = Hash::make($credentials['newPassword']);
            }

            $locationData = array_intersect_key($credentials, array_flip(['city_id', 'area_id', 'street']));

            $student->location->update($locationData);

            $student->update($credentials);

            $student->load(['subscription', 'line', 'position', 'university', 'location', 'pays']);

            $student = new UserResource($student);


            return $this->getJsonResponse($student, "Student Updated Successfully");

        } catch (Exception $exception) {

            DB::rollBack();

            return $this->getJsonResponse($exception->getMessage(), "Something Went Wrong!!", 0);

        }

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
            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }

    }

    public function activeStudentsList(): JsonResponse
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('View Student')) {

            $students = User::role('Student')->where('status', Status::Active)
                ->with(['location', 'subscription', 'line', 'position',
                    'university', 'pays', 'programs'])
                ->paginate(10);

            if ($students->isEmpty()) {

                return $this->getJsonResponse(null, "There Are No Active Students Found!");
            }
            $activeStudents = UserResource::collection($students)->response()->getData(true);

            return $this->getJsonResponse($activeStudents, "Students Fetch Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }
    }


    /**
     * Retrieves a list of all inactive students.
     */

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

            $students = User::role('Student')->where('status', Status::UnActive)
                ->with(['location', 'subscription', 'line', 'position', 'university'])
                ->paginate(10);

            if ($students->isEmpty()) {

                return $this->getJsonResponse(null, "There Are No UnActive Students Found!");
            }

            $unActiveStudents = UserResource::collection($students)->response()->getData(true);

            return $this->getJsonResponse($unActiveStudents, "Students Fetch Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }

    }

    /**
     * Confirms the attendance of a student for a specific program.
     */

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

        if (isset($data['confirmAttendance1'])) {

            $program->confirmAttendance1 = $data['confirmAttendance1'];
        }

        if (isset($data['confirmAttendance2'])) {

            $program->confirmAttendance2 = $data['confirmAttendance2'];
        }

        $program->save();

        return $this->getJsonResponse($program, "Your Attendance Is Confirmed Successfully");
    }

    /**
     * Retrieves a list of all students in a specific position for a specific trip.
     */

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

        $users = $trip->users()->whereHas('programs', function ($query) use ($position) {
            $query->where('transfer_position_id', $position->id)->where('confirmAttendance1', true);
        })->get();

        if ($users->isEmpty()) {

            return $this->getJsonResponse(null, "There are no students in this position!");
        }

        $users->load(['tripUsers']);

        $users = UserResource::collection($users);

        return $this->getJsonResponse($users, "Students fetched successfully");
    }


    function getStudentsOuterTrip(Trip $trip): JsonResponse
    {
        /**
         * @var User $auth ;
         */
        $auth = auth()->user();

        if ($auth->hasRole("Employee")) {

            $tripUsersIds = $trip->users()->select('user_id');

            $outerTripUsers = User::role('Student')->whereNotIn('id', $tripUsersIds)
                ->where('status', Status::Active)->get();

            if ($outerTripUsers->isEmpty()){

                return $this->getJsonResponse(null, "There Are No Students Found!");
            }

            return $this->getJsonResponse($outerTripUsers, "Students Fetched Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }
    }

}
