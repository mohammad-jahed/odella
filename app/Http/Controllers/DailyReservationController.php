<?php

namespace App\Http\Controllers;

use App\Enums\GuestStatus;
use App\Http\Requests\Guest\DailyReservationRequest;
use App\Http\Resources\DailyReservationResource;
use App\Models\DailyReservation;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DailyReservationController extends Controller
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
    public function show(DailyReservation $dailyReservation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DailyReservation $dailyReservation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DailyReservation $dailyReservation)
    {
        //
    }

    public function dailyReservation(DailyReservationRequest $request, Trip $trip): JsonResponse
    {
        $credentials = $request->validated();
        $credentials['guestRequestStatus'] = GuestStatus::Pending;
        $credentials['trip_id'] = $trip->id;
        $reservation = DailyReservation::query()->create($credentials);
        return $this->getJsonResponse($reservation, "Your request was sent successfully ");
    }


    public function getDailyReservation(Trip $trip)
    {
        /**
         * @var User $auth ;
         * @var DailyReservation $reservation ;
         * @var Trip $guestTrip ;
         */
        $auth = auth()->user();
        $response = [];
        if ($auth->hasRole('Supervisor') && $auth->id === $trip->supervisor->id) {
            $reservations = DailyReservation::query()->where('guestRequestStatus', GuestStatus::Pending)->get();
            foreach ($reservations as $reservation) {
                $resTrip = $reservation->trip;
                if ($resTrip->id == $trip->id) {
                    $response[] = $reservation;
                }
            }
            $response = DailyReservationResource::collection($response);
            return $this->getJsonResponse($response, "Daily Reservation Fetched Successfully");
        } else {
            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
        }
    }
}
