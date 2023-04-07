<?php

namespace App\Http\Controllers;

use App\Http\Requests\Trip\StoreTripRequest;
use App\Http\Requests\Trip\UpdateTripRequest;
use App\Models\Time;
use App\Models\TransportationLine;
use App\Models\Trip;
use App\Models\TripPositionsTimes;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TripController extends Controller
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
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('Add Trip')) {

            try {

                DB::beginTransaction();

                $credentials = $request->validated();

                $time = Time::query()->create($credentials);

                $credentials['time_id'] = $time->id;

                $trip = Trip::query()->create($credentials);

                /**
                 * @var Trip $trip ;
                 */
                /**
                 * @var TransportationLine $line ;
                 */

                $line = TransportationLine::query()->where('id', $credentials['line_id'])->first();

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
                DB::commit();

                return $this->getJsonResponse($trip, "Trip Created Successfully");

            } catch (Exception $exception) {

                DB::rollBack();

                return $this->getJsonResponse($exception->getMessage(), "Something Went Wrong!!");
            }

        } else {

            abort(Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Trip $trip)
    {
        /**
         * @var User $user ;
         */
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
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('Update Trip')) {

            $credentials = $request->validated();

            $data = [];

            if (isset($credentials['start'])) {
                $data += ['start' => $credentials['start']];
            }
            if (isset($credentials['date'])) {
                $data += ['date' => $credentials['date']];
            }

            $time = $trip->time;
            $time->update($data);

            $trip->update($credentials);

            if (isset($credentials['line_id'])) {
                $line = TransportationLine::query()->where('id', $credentials['line_id'])->first();

                $trip->lines()->sync($line);
            }

            if (isset($credentials['position_ids']) && isset($credentials['time'])) {

                $transportationTimes = TripPositionsTimes::query()->where('trip_id', $trip->id)->get();

                for ($i = 0; $i < sizeof($credentials['position_ids']); $i++) {
                    for ($j = 0; $j < sizeof($credentials['time']); $j++) {
                        if ($i == $j) {
                            $data = [
                                'position_id' => $credentials['position_ids'][$i],
                                'time' => $credentials['time'][$j],
                                'trip_id' => $trip->id
                            ];
                            $transportationTimes[$i]->update($data);
                        }
                    }
                }

            }

            return $this->getJsonResponse($trip, "Trip Updated Successfully");

        } else {
            abort(Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Trip $trip)
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('Delete Trip')) {

            $trip->delete();

            return $this->getJsonResponse($trip, "Trip Deleted Successfully");

        } else {

            abort(Response::HTTP_FORBIDDEN);
        }
    }
}
