<?php

namespace App\Http\Controllers;

use App\Enums\GuestStatus;
use App\Enums\Messages;
use App\Enums\Status;
use App\Http\Requests\Supervisor\StoreSupervisorRequest;
use App\Http\Requests\Supervisor\UpdateSupervisorRequest;
use App\Http\Resources\DailyReservationResource;
use App\Http\Resources\UserResource;
use App\Models\DailyReservation;
use App\Models\Location;
use App\Models\Trip;
use App\Models\TripPositionsTimes;
use App\Models\User;
use App\Notifications\Guests\DailyReservationNotification;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;

class SupervisorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('View Supervisor')) {

            $supervisors = User::role('Supervisor')->paginate(10);

            if ($supervisors->isEmpty()) {

                return $this->getJsonResponse(null, "There Are No Supervisors Found!");
            }

            return $this->getJsonResponse($supervisors, "Supervisors Fetched Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSupervisorRequest $request)
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('Add Supervisor')) {

            try {

                $credentials = $request->validated();

                if ($request->hasFile('image')) {

                    $path = $request->file('image')->store('images/supervisor');

                    $credentials['image'] = $path;
                }

                DB::beginTransaction();
                /**
                 * @var User $user ;
                 */

                $credentials['password'] = Hash::make($credentials['password']);

                /**
                 * @var Location $location ;
                 */
                $location = Location::query()->create($credentials);

                $credentials['location_id'] = $location->id;

                $credentials['status'] = Status::NonStudent;

                $user = User::query()->create($credentials);

                $role = Role::query()->where('name', 'like', 'Supervisor')->first();

                $user->assignRole($role);

                DB::commit();

                return $this->getJsonResponse($user, "Supervisor Registered Successfully");

            } catch (Exception $exception) {

                DB::rollBack();

                return $this->getJsonResponse($exception->getMessage(), "Something Went Wrong!!", 0);
            }

        } else {

            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $supervisor)
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('View Supervisor')) {

            return $this->getJsonResponse($supervisor, "Supervisor Fetched Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }
    }

    /**
     * Update the specified resource in storage.
     * @throws AuthorizationException
     */
    public function update(UpdateSupervisorRequest $request, User $supervisor): JsonResponse
    {
        /**
         * @var User $auth ;
         */
        $auth = auth()->user();

        Gate::forUser($auth)->authorize('updateProfile', $supervisor);

        try {

            $credentials = $request->validated();

            if ($request->hasFile('image')) {

                $path = $request->file('image')->store('images/supervisor');

                $credentials['image'] = $path;
            }

            DB::beginTransaction();

            if (isset($credentials['newPassword'])) {

                $credentials['password'] = Hash::make($credentials['newPassword']);
            }

            $locationData = array_intersect_key($credentials, array_flip(['city_id', 'area_id', 'street']));

            $supervisor->location->update($locationData);

            $supervisor->update($credentials);

            $supervisor = new UserResource($supervisor);

            DB::commit();

            return $this->getJsonResponse($supervisor, "Supervisor Updated Successfully");

        } catch (Exception $exception) {

            DB::rollBack();

            return $this->getJsonResponse($exception->getMessage(), "Something Went Wrong!!", 0);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $supervisor)
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('Delete Supervisor')) {

            $supervisor->delete();

            return $this->getJsonResponse(null, "Supervisor Deleted Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }
    }

    /**
     * Approves a daily reservation made by a supervisor.
     */
    public function approveReservation(DailyReservation $reservation): JsonResponse
    {
        /**
         * @var User $auth ;
         */
        $auth = auth()->user();

        if ($auth->hasRole('Supervisor') && $auth->id === $reservation->trip->supervisor->id) {

            $reservation->guestRequestStatus = GuestStatus::Approved;

            $reservation->save();

            $time = TripPositionsTimes::query()->where('trip_id', $reservation->trip_id)->where('position_id', $reservation->transfer_position_id)->get(['time']);

            $reservation->notify(new DailyReservationNotification(true, $time));

            return $this->getJsonResponse(new DailyReservationResource($reservation), "Reservation has been Approved successfully");

        } else {
            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }

    }

    /**
     * Denies a daily reservation made by a supervisor.
     */
    public function denyReservation(DailyReservation $reservation): JsonResponse
    {
        /**
         * @var User $auth ;
         */
        $auth = auth()->user();

        if ($auth->hasRole('Supervisor') && $auth->id === $reservation->trip->supervisor->id) {

            $time = TripPositionsTimes::query()->where('trip_id', $reservation->trip_id)->where('position_id', $reservation->transfer_position_id)->get(['time']);

            $reservation->guestRequestStatus = GuestStatus::Rejected;

            $reservation->save();

            $reservation->notify(new DailyReservationNotification(false, $time));

            return $this->getJsonResponse(new DailyReservationResource($reservation), "Reservation has been Rejected successfully");

        } else {
            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }
    }

    public function qrConfirmAttendance(Trip $trip, User $user)
    {
        /**
         * @var User $auth ;
         */
        $auth = auth()->user();

        if ($auth->hasRole('Supervisor')) {

            $tripUser = $trip->users()->where('user_id', $user->id)->firstOrFail();

            if ($tripUser->status != Status::Active) {
                return $this->getJsonResponse(null, "This Student is not Subscribe", 0);
            }

            if (isset($tripUser->pivot)) {
                $tripUser->pivot->studentAttendance = true;
            }

            if (isset($tripUser->pivot)) {
                $tripUser->pivot->save();
            }

            return $this->getJsonResponse(null, "Student AttendanceConfirm Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }
    }

}
