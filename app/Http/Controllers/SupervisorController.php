<?php

namespace App\Http\Controllers;

use App\Enums\GuestStatus;
use App\Enums\Status;
use App\Enums\TripStatus;
use App\Http\Requests\Supervisor\StoreSupervisorRequest;
use App\Http\Requests\Supervisor\SupervisorTripRequest;
use App\Http\Requests\Supervisor\UpdateSupervisorRequest;
use App\Http\Resources\DailyReservationResource;
use App\Http\Resources\TripResource;
use App\Http\Resources\UserResource;
use App\Models\DailyReservation;
use App\Models\Day;
use App\Models\Location;
use App\Models\Program;
use App\Models\Trip;
use App\Models\TripPositionsTimes;
use App\Models\User;
use App\Notifications\Guests\DailyReservationNotification;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Date;
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

            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
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

                return $this->getJsonResponse($exception->getMessage(), "Something Went Wrong!!");
            }

        } else {

            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
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

            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
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

            return $this->getJsonResponse($exception->getMessage(), "Something Went Wrong!!");
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

            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Don't Have Permission To Access This Action");
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

            $time = TripPositionsTimes::query()->where('trip_id', $reservation->trip_id)
                ->where('position_id', $reservation->transfer_position_id)
                ->get(['time']);

            $reservation->notify(new DailyReservationNotification(true, $time));

            return $this->getJsonResponse(
                new DailyReservationResource($reservation),
                "Reservation has been Approved successfully"
            );
        } else {
            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
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

            $reservation->guestRequestStatus = GuestStatus::Rejected;

            $reservation->save();

            $reservation->notify(new DailyReservationNotification(false));

            return $this->getJsonResponse(
                new DailyReservationResource($reservation),
                "Reservation has been Rejected successfully"
            );
        } else {
            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
        }
    }


    public function supervisor_current_trip(SupervisorTripRequest $request): JsonResponse
    {
        /**
         * @var User $supervisor ;
         */
        $supervisor = auth()->user();

        $dayOfWeek = Date::now()->dayOfWeek;
        /**
         * @var Day $day ;
         */
        $day = Day::query()->find($dayOfWeek);
        /**
         * @var Program $got_trip ;
         */
        $got_trip = $supervisor->programs()
            ->where('day_id', $dayOfWeek)
            ->where('start', '!=', '00:00:00')
            ->whereTime('start', '<=', $request->time)
            ->WhereTime('end', '>', $request->time)
            ->orWhere('end','=','00:00:00')
            ->first();

        /**
         * @var Program $return_trip ;
         */
        $return_trip = $supervisor->programs()
            ->where('day_id', $dayOfWeek)
            ->where('end', '!=', '00:00:00')
            ->WhereTime('end', '<=', $request->time)
            ->first();

        /**
         * @var Trip $current_trip ;
         */
        $current_trip = null;

        if ($got_trip) {

            $current_trip = Trip::query()->where('supervisor_id', $supervisor->id)
                ->where('status', TripStatus::GoTrip)
                ->whereHas('time', function ($query) use ($got_trip, $day) {
                    $query->where('start', $got_trip->start)
                        ->whereRaw("DayName(date) = '$day->name_en'");
                })->first();
        }

        if ($return_trip) {

            $current_trip = Trip::query()->where('supervisor_id', $supervisor->id)
                ->where('status', TripStatus::ReturnTrip)
                ->whereHas('time', function ($query) use ($return_trip, $day) {
                    $query->where('start', $return_trip->end)
                        ->whereRaw("DayName(date) = '$day->name_en'");
                })->first();
        }

        if (!$current_trip) {

            return $this->getJsonResponse(null, "You Have No Trips Today");
        }

        $current_trip->load(['time', 'lines', 'transferPositions', 'users']);

        $current_trip = new TripResource($current_trip);

        return $this->getJsonResponse($current_trip, "Trip Fetched Successfully");

    }

}
