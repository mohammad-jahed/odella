<?php

namespace App\Http\Controllers;

use App\Http\Requests\Student\StoreTripStudentsRequest;
use App\Http\Requests\Trip\StoreTripRequest;
use App\Http\Requests\Trip\UpdateTripRequest;
use App\Models\Program;
use App\Models\Time;
use App\Models\TransportationLine;
use App\Models\Trip;
use App\Models\TripPositionsTimes;
use App\Models\TripUser;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
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

                /**
                 * @var Time $time ;
                 * @var Trip $trip ;
                 */
                $time = Time::query()->create($credentials);

                $credentials['time_id'] = $time->id;

                $trip = Trip::query()->create($credentials);

                /**
                 *
                 */
                /**
                 * @var TransportationLine $line ;
                 */

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


    public function addStudents(StoreTripStudentsRequest $request, Trip $trip): JsonResponse
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();
        if ($user->hasRole('Employee')) {
            $data = $request->validated();
            $trip->users()->attach($data['student_ids']);
            $day = $trip->time->date->format('l');
            foreach ($data['student_ids'] as $student_id) {
                /**
                 * @var User $student ;
                 * @var Program $program
                 */
                $student = User::query()->where('id', $student_id)->first();
                $programs = $student->programs;
                foreach ($programs as $program) {
                    if ($program->day->name_en == $day) {
                        if ($trip->status == 0) {
                            $attributes = [
                                'start' => $trip->time->start
                            ];
                        } else {
                            $attributes = [
                                'end' => $trip->time->start
                            ];
                        }
                        $program->update($attributes);
                    }
                }
            }
            $trip->load('users');
            return $this->getJsonResponse($trip, 'Students Added Successfully To This Trip');
        } else {
            abort(Response::HTTP_FORBIDDEN);

        }
    }


    public function deleteStudent(Trip $trip, User $student): JsonResponse
    {
        /**
         * @var Program $program ;
         * @var User $user ;
         */
        $user = auth()->user();
        if ($user->hasRole('Employee')) {
            $day = $trip->time->date->format('l');
            $programs = $student->programs;
            foreach ($programs as $program) {
                if ($program->day->name_en == $day) {
                    if ($trip->status == 0) {
                        $attributes = [
                            'start' => '00:00:00'
                        ];
                    } else {
                        $attributes = [
                            'end' => '00:00:00'
                        ];
                    }
                    $program->update($attributes);
                    if ($program->start == '00:00:00' && $program->end == '00:00:00') {
                        $program->delete();
                    }
                }
            }
            TripUser::query()->where('user_id', $student->id)->delete();
            $trip->load('users');
            return $this->getJsonResponse($trip, "Student Deleted Successfully");
        } else {
            abort(Response::HTTP_FORBIDDEN);
        }
    }
}
