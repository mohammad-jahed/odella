<?php

namespace App\Http\Controllers;

use App\Http\Requests\Trip\StoreTripRequest;
use App\Http\Requests\Trip\UpdateTripRequest;
use App\Models\Time;
use App\Models\TransportationLine;
use App\Models\Trip;
use App\Models\TripPositionsTimes;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TripController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $user = auth()->user();

        if ($user->can('View Trips')) {

            $trips = Trip::all();

            return $this->getJsonResponse($trips, "Trips Fetched Successfully");

        } else {

            abort(Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTripRequest $request)
    {

        $user = auth()->user();

        if ($user->can('Add Trip')) {

            DB::transaction(function () use ($request) {

                $credentials = $request->validated();

                $time = Time::query()->create($credentials);

                $credentials['time_id'] = $time->id;

                $trip = Trip::query()->create($credentials);

                $line = TransportationLine::where('id', $credentials['line_id'])->first();

                $trip->lines()->attach($line);
                $trip = $trip->load('lines', 'time', 'busDriver');

                $positionsNumber = $line->positions()->count();

                for ($i = 0; $i < $positionsNumber; $i++) {
                    for ($j = 0; $j < $positionsNumber; $j++) {
                        if ($i == $j) {
                            $data = [
                                'position_id' => $credentials['position_ids'][$i],
                                'time' => $credentials['time'][$j],
                                'trip_id' => $trip->id
                            ];
                            TripPositionsTimes::query()->create($data);
                        }
                    }
                }
                return $this->getJsonResponse($trip, "Trip Created Successfully");

            });


        } else {

            abort(Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Trip $trip)
    {
        $user = auth()->user();

        if ($user->can('View Trips')) {

            return $this->getJsonResponse($trip, "Trip Fetched Successfully");

        } else {

            abort(Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTripRequest $request, Trip $trip)
    {
        $user = auth()->user();

        if ($user->can('Update Trip')) {

        } else {
            abort(Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Trip $trip)
    {
        $user = auth()->user();

        if ($user->can('Delete Trip')) {

            $trip->delete();

            return $this->getJsonResponse($trip, "Trip Deleted Successfully");

        } else {

            abort(Response::HTTP_FORBIDDEN);
        }
    }
}
