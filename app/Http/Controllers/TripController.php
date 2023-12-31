<?php

namespace App\Http\Controllers;

use App\Enums\Messages;
use App\Enums\TripStatus;
use App\Http\Requests\Guest\TodayTripsRequest;
use App\Http\Requests\Student\StoreTripStudentsRequest;
use App\Http\Requests\Supervisor\SupervisorTripRequest;
use App\Http\Requests\Trip\StoreTripRequest;
use App\Http\Requests\Trip\UpdateTripRequest;
use App\Http\Requests\Trips\GenerateTripsRequest;
use App\Http\Resources\EvaluationResource;
use App\Http\Resources\TripResource;
use App\Models\AlgorithmInput;
use App\Models\Day;
use App\Models\Program;
use App\Models\Time;
use App\Models\TransferPosition;
use App\Models\TransportationLine;
use App\Models\Trip;
use App\Models\TripPositionsTimes;
use App\Models\TripUser;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Kutia\Larafirebase\Facades\Larafirebase;
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

            $trips = Trip::query()->with(['supervisor', 'time', 'lines', 'transferPositions',
                'users', 'busDriver'])->paginate(10);

            if ($trips->isEmpty()) {

                return $this->getJsonResponse(null, "There Are No Trips Found!");
            }

            $trips = TripResource::collection($trips)->response()->getData(true);

            return $this->getJsonResponse($trips, "Trips Fetched Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
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
                $credentials['status'] = ($credentials['start'] >= "06:00" && $credentials['start'] < "12:00") ? TripStatus::GoTrip : TripStatus::ReturnTrip;

                /**
                 * @var Time $time ;
                 * @var Trip $trip ;
                 * @var TransportationLine $line ;
                 */

                $credentials['day'] = Carbon::createFromFormat('Y-m-d', Carbon::parse($credentials['date'])->format('Y-m-d'))->format('l');

                $time = Time::query()->create($credentials);

                $credentials['time_id'] = $time->id;

                $trip = Trip::query()->create($credentials);

                $trip->lines()->attach($credentials['line_ids']);

                $trip = $trip->load('lines', 'time', 'busDriver');

                foreach ($credentials['line_ids'] as $line_id) {

                    $line = TransportationLine::query()->where('id', $line_id)->first();

                    $positionsNumber = $line->positions()->count();

                    for ($i = 0; $i < $positionsNumber; $i++) {

                        $data = [
                            'position_id' => $credentials['position_ids'][$i],
                            'time' => $credentials['time'][$i],
                            'trip_id' => $trip->id
                        ];

                        TripPositionsTimes::query()->create($data);
                    }
                }

                /**
                 * @var Program[] $programs ;
                 * @var Day $day ;
                 */
                $dayName = Carbon::parse($credentials['date'])->format('l');

                $day = Day::query()->firstWhere('name_en', $dayName);

                //get all supervisor programs
                $programs = Program::query()
                    ->where('user_id', $credentials['supervisor_id'])
                    ->when($dayName, function ($query, $dayName) {
                        $query->whereHas('day', fn(Builder $builder) => $builder->where('name_en', $dayName));
                    })->get();

                if (!$programs->isEmpty()) {

                    $addField = true;

                    foreach ($programs as $program) {

                        if ($trip->status == TripStatus::GoTrip && $program->start == "00:00:00") {
                            $program->start = $trip->time->start;
                            $addField = false;
                        }

                        if ($trip->status == TripStatus::ReturnTrip && $program->end == "00:00:00") {
                            $program->end = $trip->time->start;
                            $addField = false;
                        }

                        $program->save();

                        if ($addField) {
                            $data = [
                                'user_id' => $credentials['supervisor_id'],
                                'day_id' => $day->id,
                                ($trip->status == TripStatus::GoTrip ? 'start' : 'end') => $trip->time->start
                            ];

                            Program::query()->create($data);
                        }
                    }
                } else {
                    $data = [
                        'user_id' => $credentials['supervisor_id'],
                        'day_id' => $day->id,
                        ($trip->status == TripStatus::GoTrip ? 'start' : 'end') => $trip->time->start
                    ];

                    Program::query()->create($data);
                }

                DB::commit();

                $trip = new TripResource($trip);

                return $this->getJsonResponse($trip, "Trip Created Successfully");

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
    public function show(Trip $trip)
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('View Trips')) {

            $trip->load(['supervisor', 'time', 'lines', 'transferPositions', 'users', 'busDriver']);

            $trip = new TripResource($trip);

            return $this->getJsonResponse($trip, "Trip Fetched Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
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

            try {

                DB::beginTransaction();

                $credentials = $request->validated();

                $data = [];

                $dayName = Carbon::parse($trip->time->date)->format('l');
                /**
                 * @var Day $day ;
                 */
                $day = Day::query()->firstWhere('name_en', $dayName);

                $oldSupervisor = $trip->supervisor;

                $oldSupervisorProgram = $oldSupervisor->programs()
                    ->where('day_id', $day->id)
                    ->where(($trip->status == TripStatus::GoTrip ? 'start' : 'end'), $trip->time->start)
                    ->first();

                if (isset($credentials['supervisor_id'])) {

                    $oldSupervisorProgram->delete();

                    $dayName1 = Carbon::parse(($credentials['date'] ?? $trip->time->date))->format('l');
                    /**
                     * @var Day $day1 ;
                     */
                    $day1 = Day::query()->firstWhere('name_en', $dayName1);

                    if (isset($credentials['status'])) {

                        $ptime = ($credentials['status'] == TripStatus::GoTrip ? 'start' : 'end');
                    }

                    Program::query()->create([
                        'user_id' => $credentials['supervisor_id'],
                        'day_id' => $day1->id,
                        ($ptime ?? $trip->status == TripStatus::GoTrip ? 'start' : 'end')
                        => ($credentials['start'] ?? $trip->time->start)
                    ]);
                }

                if (isset($credentials['start'])) {

                    $data += ['start' => $credentials['start']];

                    $credentials['status'] = ($credentials['start'] > "07:00" && $credentials['start'] < "11:00") ? 1 : 2;

                }

                if (isset($credentials['date'])) {

                    $data += [
                        'date' => $credentials['date'],
                        'day' => Carbon::createFromFormat('Y-m-d', Carbon::parse($credentials['date'])->format('Y-m-d'))->format('l')
                    ];
                }

                $time = $trip->time;

                $trip->load(['supervisor', 'lines', 'transferPositions', 'users', 'busDriver']);

                $time->update($data);

                $trip->update($credentials);

                if (!isset($credentials['supervisor_id'])) {

                    $dayName1 = Carbon::parse(($credentials['date'] ?? $trip->time->date))->format('l');
                    /**
                     * @var Day $day1 ;
                     */
                    $day1 = Day::query()->firstWhere('name_en', $dayName1);

                    $newProgramData = [
                        'day_id' => $day1->id,
                        ($trip->status == TripStatus::GoTrip ? 'start' : 'end') => $trip->time->start
                    ];

                    $oldSupervisorProgram->update($newProgramData);
                }

                if (isset($credentials['line_id'])) {

                    $line = TransportationLine::query()->where('id', $credentials['line_id'])->first();

                    $trip->lines()->sync($line);
                }

                if (isset($credentials['position_ids']) && isset($credentials['time'])) {

                    $transportationTimes = TripPositionsTimes::query()->where('trip_id', $trip->id)->get();

                    $users_ids = $trip->users()->pluck('user_id');

                    $programs = Program::query()->whereIn('user_id', $users_ids)
                        ->where('day_id', $day->id)->get();

                    for ($i = 0; $i < sizeof($credentials['position_ids']); $i++) {

                        for ($j = 0; $j < sizeof($credentials['time']); $j++) {

                            if ($i == $j) {

                                $data = [
                                    'position_id' => $credentials['position_ids'][$i],
                                    'time' => $credentials['time'][$j],
                                    'trip_id' => $trip->id
                                ];

                                $transportationTimes[$i]->update($data);

                                foreach ($programs as $program) {
                                    if ($program->transfer_position_id == $data['position_id']) {
                                        $program->update([
                                            ($trip->status == TripStatus::GoTrip ? 'start' : 'end') => $data['time']
                                        ]);
                                    }
                                }
                            }
                        }
                    }

                }

                DB::commit();

                $trip = new TripResource($trip);

                return $this->getJsonResponse($trip, "Trip Updated Successfully");

            } catch (Exception $exception) {

                DB::rollBack();

                return $this->getJsonResponse($exception->getMessage(), "Something Went Wrong!!", 0);
            }

        } else {

            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
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

            return $this->getJsonResponse(null, "Trip Deleted Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }
    }

    /**
     * Adds one or more students to a specific trip.
     */
    public function addStudents(StoreTripStudentsRequest $request, Trip $trip): JsonResponse
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->hasRole('Employee')) {

            try {

                DB::beginTransaction();

                $data = $request->validated();

                $trip->users()->attach($data['student_ids']);
                /**
                 * @var DateTime $date ;
                 */
                $date = $trip->time->date;
                $day = $date->format('l');

                foreach ($data['student_ids'] as $student_id) {
                    /**
                     * @var User $student ;
                     * @var Program $program ;
                     * @var TripPositionsTimes $goTime ;
                     */

                    $student = User::query()->where('id', $student_id)->first();

                    $programs = $student->programs;

                    foreach ($programs as $program) {

                        if ($program->day->name_en == $day) {

                            $firstPosition = $trip->transferPositions()->first();

                            $goTime = TripPositionsTimes::query()->where('position_id', $firstPosition->id)->first();

                            if ($trip->status == 1) {

                                $attributes = [
                                    'start' => $goTime->time
                                ];
                            } else {

                                $attributes = [
                                    'end' => $trip->time->start
                                ];
                            }

                            $program->update($attributes);
                        } else {
                            /**
                             * @var Day $day ;
                             */
                            $day = Day::query()->where('name_en', $day)->first();
                            $attributes = [
                                'user_id' => $student_id,
                                'day_id' => $day->id,
                            ];
                            if ($trip->status == TripStatus::GoTrip) {
                                $firstPosition = $trip->transferPositions()->first();
                                $goTime = TripPositionsTimes::query()->where('position_id', $firstPosition->id)->first();
                                $attributes['start'] = $goTime->time;
                            } else {
                                $attributes['end'] = $trip->time->start;
                            }

                            Program:: query()->create($attributes);
                        }
                    }
                }

                DB::commit();

                $trip->load('users');

                $trip = new TripResource($trip);

                return $this->getJsonResponse($trip, 'Students Added Successfully To This Trip');

            } catch (Exception $exception) {
                DB::rollBack();

                return $this->getJsonResponse($exception->getMessage(), "Something Went Wrong!!", 0);
            }

        } else {

            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }
    }

    /**
     * Deletes a specific student from a specific trip.
     */
    public
    function deleteStudent(Trip $trip, User $student): JsonResponse
    {
        /**
         * @var Program $program ;
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->hasRole('Employee')) {

            try {

                DB::beginTransaction();
                /**
                 * @var DateTime $date ;
                 */
                $date = $trip->time->date;
                $day = $date->format('l');

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

                DB::commit();

                $trip->load('users');

                $trip = new TripResource($trip);

                return $this->getJsonResponse($trip, "Student Deleted Successfully");

            } catch (Exception $exception) {
                DB::rollBack();

                return $this->getJsonResponse($exception->getMessage(), "Something Went Wrong!!", 0);
            }

        } else {

            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }
    }

    /**
     * Get all trips for a specific transportation line.
     */
    public function tripsLine(TransportationLine $transportationLine): JsonResponse
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('View Trips')) {

            $trips = $transportationLine->trips;

            if ($trips->isEmpty()) {

                return $this->getJsonResponse(null, "There Are No Trips Found!");
            }

            $trips = TripResource::collection($trips);

            return $this->getJsonResponse($trips, "Trips Fetched Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }

    }


    public function getStudentTrips(): JsonResponse
    {
        /**
         * @var User $auth ;
         */
        $auth = auth()->user();

        $trips = $auth->trips;

        $trips = TripResource::collection($trips);

        return $this->getJsonResponse($trips, "Trips Fetched Successfully");

    }

    public function getPreviousWeekStudentTrips(): JsonResponse
    {
        /**
         * @var User $auth ;
         */
        $auth = auth()->user();

        $today = Carbon::now(); // Replace with your original date
        $beforeWeek = $today->copy()->subDays(7);


        $trips = $auth->trips()->with(['time', 'busDriver'])->whereHas('time',
            fn(Builder $builder) => $builder->whereBetween('date', [$beforeWeek, $today])
        )->get();
        $trips = TripResource::collection($trips);

        $evaluations = $auth->evaluations()->with(['trip'])->whereHas('trip',
            fn(Builder $builder) => $builder->whereHas('time',
                fn(Builder $builder) => $builder->whereBetween('date', [$beforeWeek, $today])
            )
        )->get();
        $evaluations = EvaluationResource::collection($evaluations);

        $response = [
            'trips' => $trips,
            'evaluations' => $evaluations
        ];

        return $this->getJsonResponse($response, "Trips Fetched Successfully");

    }

    public function current_trips(SupervisorTripRequest $request): JsonResponse
    {
        $dayOfWeek = Date::now()->dayOfWeekIso;

        /**
         * @var Day $day;
         */
        $day = Day::query()->find($dayOfWeek);

        $got_trip = Program::query()
            ->where('day_id', $dayOfWeek)
            ->where('start', '!=', '00:00:00')
            ->whereTime('start', '<=', $request->time)
            ->whereTime('end', '>', $request->time)
            ->orWhere('end', '=', '00:00:00')
            ->get();

        $return_trip = Program::query()
            ->where('day_id', $dayOfWeek)
            ->where('end', '!=', '00:00:00')
            ->whereTime('end', '<=', $request->time)
            ->get();

        $current_trips = collect();

        foreach ($got_trip as $trip) {
            $current_trip = Trip::query()
                ->where('status', TripStatus::GoTrip)
                ->whereHas('time', function ($query) use ($trip, $day) {
                    $query->where('start', $trip->start)
                        ->whereRaw("DayName(date) = '$day->name_en'");
                })->first();

            if ($current_trip) {
                $current_trips->push($current_trip);
            }
        }

        foreach ($return_trip as $trip) {
            $current_trip = Trip::query()
                ->where('status', TripStatus::ReturnTrip)
                ->whereHas('time', function ($query) use ($trip, $day) {
                    $query->where('start', $trip->end)
                        ->whereRaw("DayName(date) = '$day->name_en'");
                })->first();

            if ($current_trip) {
                $current_trips->push($current_trip);
            }
        }

        if ($current_trips->isEmpty()) {
            return $this->getJsonResponse(null, "There are no current trips for any user");
        }

        $current_trips->load(['time', 'lines', 'transferPositions', 'users']);

        $current_trips = TripResource::collection($current_trips);

        return $this->getJsonResponse($current_trips, "Current trips fetched successfully");
    }

    public function getGoTrips(): JsonResponse
    {
        /**
         * @var User $auth ;
         */
        $auth = auth()->user();

        if ($auth->can('View Trips')) {

            $trips = Trip::query()->where('status', 1)->with('time')->paginate(10);

            if ($trips->isEmpty()) {

                return $this->getJsonResponse(null, "There Are No Trips Found!");
            }

            $trips = TripResource::collection($trips)->response()->getData(true);

            return $this->getJsonResponse($trips, "Go Trips Fetched Successfully!!");

        } else {
            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }
    }

    public function getReturnTrips(): JsonResponse
    {
        /**
         * @var User $auth ;
         */
        $auth = auth()->user();

        if ($auth->can('View Trips')) {

            $trips = Trip::query()->where('status', 2)->with('time')->paginate(10);

            if ($trips->isEmpty()) {

                return $this->getJsonResponse(null, "There Are No Trips Found!");
            }

            $trips = TripResource::collection($trips)->response()->getData(true);

            return $this->getJsonResponse($trips, "Return Trips Fetched Successfully!!");

        } else {
            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }
    }


    public function supervisor_current_trip(SupervisorTripRequest $request): JsonResponse
    {
        /**
         * @var User $supervisor ;
         */
        $supervisor = auth()->user();

        $dayOfWeek = Date::now()->dayOfWeekIso;
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
            ->orWhere('end', '=', '00:00:00')
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
            if ($supervisor->hasRole('Supervisor')) {
                $current_trip = Trip::query()->where('supervisor_id', $supervisor->id)
                    ->where('status', TripStatus::GoTrip)
                    ->whereHas('time', function ($query) use ($got_trip, $day) {
                        $query->where('start', $got_trip->start)
                            ->whereRaw("DayName(date) = '$day->name_en'");
                    })->first();
            } else {
                $current_trip = Trip::query()->whereHas('users',
                    fn($query) => $query->where('user_id', $supervisor->id)
                )->where('status', TripStatus::GoTrip)
                    ->whereHas('time', function ($query) use ($got_trip, $day) {
                        $query->where('start', '<=', $got_trip->start)
                            ->whereRaw("DayName(date) = '$day->name_en'");
                    })->first();
            }
        }

        if ($return_trip) {

            if ($supervisor->hasRole('Supervisor')) {
                $current_trip = Trip::query()->where('supervisor_id', $supervisor->id)
                    ->where('status', TripStatus::ReturnTrip)
                    ->whereHas('time', function ($query) use ($return_trip, $got_trip, $day) {
                        $query->where('start', $return_trip->end)
                            ->whereRaw("DayName(date) = '$day->name_en'");
                    })->first();
            } else {
                $current_trip = Trip::query()->whereHas('users',
                    fn(Builder $builder) => $builder->where('user_id', $supervisor->id)
                )->where('status', TripStatus::ReturnTrip)
                    ->whereHas('time', function ($query) use ($return_trip, $got_trip, $day) {
                        $query->where('start', $return_trip->end)
                            ->whereRaw("DayName(date) = '$day->name_en'");
                    })->first();
            }
        }

        if (!$current_trip) {

            return $this->getJsonResponse(null, "You Have No Trips Today");
        }

        $current_trip->load([
            'time',
            'lines',
            'transferPositions',
            'users.tripUsers' => function($query) use ($current_trip) {
                return $query->where('trip_id', $current_trip->id);
            }
        ]);

        $current_trip = new TripResource($current_trip);

        return $this->getJsonResponse($current_trip, "Trip Fetched Successfully");

    }

    public function getWeeklyTripsBeforeToday()
    {
        /**
         * @var User $user
         */
        $user = auth()->user();
        if ($user->hasRole('Student') || $user->hasRole('Supervisor')) {
            $today = Carbon::now();
            $startOfWeek = Carbon::now()->startOfWeek();
            $evaluations = $user->evaluations()->with(['trip'])->get();
            $evaluations = EvaluationResource::collection($evaluations);
            $trips = $user->trips()->with(['time'])->whereHas('time',
                fn(Builder $builder) => $builder->whereBetween('date', [$startOfWeek, $today])
            )->get();
            $trips = TripResource::collection($trips);
            $trips['evaluations'] = $evaluations;
            return $this->getJsonResponse($trips, "Trips Fetched Successfully");

        } else {
            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }
    }

    public function todayTrips(TodayTripsRequest $request): JsonResponse
    {
        $dayOfWeek = Date::now()->dayOfWeekIso;
        /**
         * @var Day $day ;
         */
        $day = Day::query()->find($dayOfWeek);

        $today_trips = Trip::query()->whereHas('time', function ($query) use ($day, $request) {
            $query->where('start','<=', $request->time)
                    ->whereRaw("DayName(date) = '$day->name_en'");
            })->get();

        if ($today_trips->isEmpty()) {

            return $this->getJsonResponse(null, "There are No Trips Today");
        }

        $today_trips->load(['time', 'lines', 'transferPositions']);

        $today_trips = TripResource::collection($today_trips);

        return $this->getJsonResponse($today_trips, "Trips Fetched Successfully");
    }

    public function generateTrips(GenerateTripsRequest $request): JsonResponse
    {
        $data = $request->validated();

        $initialGoTrips = [
            new \DateTime('07:00'),
            new \DateTime('07:30'),
            new \DateTime('08:00'),
            new \DateTime('08:30'),
            new \DateTime('09:00'),
            new \DateTime('09:30'),
            new \DateTime('10:00'),
        ];

        $initialReturnTrips = [
            new \DateTime('12:00'),
            new \DateTime('12:30'),
            new \DateTime('13:00'),
            new \DateTime('13:30'),
            new \DateTime('14:00'),
            new \DateTime('14:30'),
            new \DateTime('15:00'),
        ];
        $goTripsResponse = [];

        foreach ($initialGoTrips as $goTimeTrip) {
            $goTimeTrip = $goTimeTrip->format('h:i A');
            $usersNumber = User::query()->whereHas('algorithm_inputs',
                fn(Builder $builder) => $builder->whereTime('goTime', $goTimeTrip)
                    ->where('day_id', $data['day_id'])
            )->count();

            $goTripsResponse[$goTimeTrip] = $usersNumber;
        }

        arsort($goTripsResponse);

        $slicedArray = array_slice($goTripsResponse, 0, $data['goTripsNumber'], true);
        $sumBeyondN = array_sum(array_slice($goTripsResponse,  $data['goTripsNumber']));
        end($slicedArray);
        $key = key($slicedArray);
        $slicedArray[$key] += $sumBeyondN;
        $nGoTrip = $slicedArray;


        $returnTripsResponse = [];

        foreach ($initialReturnTrips as $returnTimeTrip) {
            $returnTimeTrip = $returnTimeTrip->format('H:i:s');
            $usersNumber1 = User::query()->whereHas('algorithm_inputs',
                fn(Builder $builder) => $builder->where('returnTime', $returnTimeTrip)
                    ->where('day_id', $data['day_id'])
            )->count();

            $returnTripsResponse[$returnTimeTrip] = $usersNumber1;
        }

        arsort($returnTripsResponse);

        $slicedArray1 = array_slice($returnTripsResponse, 0, $data['returnTripsNumber'], true);
        $sumBeyondN1 = array_sum(array_slice($returnTripsResponse,  $data['returnTripsNumber']));
        end($slicedArray1);
        $key1 = key($slicedArray1);
        $slicedArray1[$key1] += $sumBeyondN1;
        $mReturnTrip = $slicedArray1;

        $response = [
            'goTrips' => $nGoTrip,
            'returnTrips' => $mReturnTrip
        ];

        $newData = [];

        foreach ($response as $tripType => $trips) {
            $newTrips = [];

            foreach ($trips as $time => $studentNumber) {
                $newTrips[] = [
                    "time" => $time,
                    "studentNumber" => $studentNumber
                ];
            }

            $newData[$tripType] = $newTrips;
        }

        return $this->getJsonResponse($newData, "Suggestions for trip generation created successfully");
    }


    public function sendNotification()
    {
        return Larafirebase::withTitle('Test Title1')
            ->withBody('Test body1')
            ->withSound('default')
            ->withPriority('high')
            ->withAdditionalData([
                'color' => '#rrggbb',
                'badge' => 0,
            ])
            ->sendNotification('f3blYFjIc9TbMNmILvfYh2:APA91bHU9WqR7RHiLtwKU9oDeQXgk42TEkfW1_bA2MZGfxIAqzWTHWncCEum4aDGg5KkFsbpdB5sLh2OKWGhneS-ccgAXvXZiL5xPyG6l6RVI-RNfa-a7HcMT57Uc1Scl1bKvQv0W3kv');
    }

    public
    function test_go_trips_notification()
    {

//        /**
//         * @var array $trip_ids
//         */
//        /**
//         * @var array $user_ids
//         */
//        /**
//         * @var array $position_ids
//         */
//        /**
//         * @var User $user
//         */
//
//        $date = Date::now()->toDateString();
//        $trips = Trip::query()->where('status', TripStatus::GoTrip)
//            ->whereHas('time',
//                function (Builder $builder1) use ($date) {
//                    $builder1->where('date', '=', $date);
//                })->get();
//
//
//        foreach ($trips as $trip)
//            $trip_ids[] = $trip->id;
//
//        return $trip_ids;
//
//        $users = User::query()->whereHas('trips',
//            function (Builder $builder1) use ($trip_ids) {
//                $builder1->wherein('trip_id', $trip_ids);
//            })->get();
//
//        foreach ($users as $user)
//            $user_ids[] = $user->id;
//
//        $positions = TransferPosition::query()->whereHas('trips', function (Builder $builder) use ($trip_ids) {
//            $builder->wherein('trip_id', $trip_ids);
//        })->get();
//
//        foreach ($positions as $position)
//            $position_ids[] = $position->id;
//
//
//        $day = Date::now()->dayOfWeek;
//
//        $programes = Program::query()->where('day_id', $day)
//            ->where(['confirmAttendance1' => true])
//            ->wherein('user_id', $user_ids)
//            ->wherein('transfer_position_id', $position_ids)
//            ->get();
//
//        foreach ($programes as $programe) {
//            $vv = Date::now()->diffInMinutes($programe->start, false);
//            if ($vv <= 5 && $vv > 3) {
//                $user = User::query()->where('id', $programe->user_id)->first();
//                //$user->notify(new PositionTimeNotification($user));
//                return $user;
//            }
//
//        }
//        return 'ok';


        /**
         * @var User $user
         */

        $date = Date::now()->toDateString();

        $day = Date::now()->dayOfWeek;

        $programs = Program::query()
            ->where('day_id', $day)
            ->where(['confirmAttendance1' => true])
            ->whereHas('user', function ($query) use ($date) {
                $query->whereHas('trips', function ($query) use ($date) {
                    $query->where('status', TripStatus::GoTrip)
                        ->whereHas('time', function ($query) use ($date) {
                            $query->where('date', $date);
                        });
                });
            })->get();

        foreach ($programs as $program) {
            $remainTime = Date::now()->diffInMinutes($program->start, false);
            if ($remainTime <= 5 && $remainTime > 3) {

                $user = $program->user_id;
                //$user->notify(new PositionTimeNotification($user));
            }

        }

//        $remainTime = Date::now()->diffInMinutes($programe->start, false);
//       $pp= Program::query()->where('day_id', $day)
//            ->where('confirmAttendance1', true)
//            ->whereIn('user_id', $users)
//            ->whereIn('transfer_position_id', $positions)
//           ->whereBetween('start',[now()->diffInMinutes('start'),now()->addMinutes(1)])
//            //->whereTime('start', '>=', now()->addMinutes(5))
//            //->whereTime('start', '<', now()->addMinutes(1))
//            ->with('user')
//            ->get();
//        return $pp;
//            //->each(fn($program) => $program->user->notify(new PositionTimeNotification($program->user)));


//        $date = Date::now()->diffInDays('2023-03-17',false)<=30;
//        return $date;

    }


    public
    function test_return_trips_notification()
    {

        /**
         * @var array $trip_ids
         */
        /**
         * @var array $user_ids
         */
        /**
         * @var array $position_ids
         */
        /**
         * @var User $user
         */
        $date = Date::now()->toDateString();
        $trips = Trip::query()->where('status', TripStatus::ReturnTrip)
            ->whereHas('time',
                function (Builder $builder1) use ($date) {
                    $builder1->where('date', '=', $date);
                })->get();

        foreach ($trips as $trip)
            $trip_ids[] = $trip->id;

        $users = User::query()->whereHas('trips',
            function (Builder $builder1) use ($trip_ids) {
                $builder1->wherein('trip_id', $trip_ids);
            })->get();

        foreach ($users as $user)
            $user_ids[] = $user->id;

        $positions = TransferPosition::query()->whereHas('trips', function (Builder $builder) use ($trip_ids) {
            $builder->wherein('trip_id', $trip_ids);
        })->get();

        foreach ($positions as $position)
            $position_ids[] = $position->id;


        $day = Date::now()->dayOfWeek;

        $programes = Program::query()->where('day_id', $day)
            ->where(['confirmAttendance2' => true])
            ->wherein('user_id', $user_ids)
            ->wherein('transfer_position_id', $position_ids)
            ->get();

        foreach ($programes as $programe) {
            $remainTime = Date::now()->diffInMinutes($programe->end, false);
            if ($remainTime <= 15 && $remainTime > 10) {

                $user = User::query()->where('id', $programe->user_id)->first();
                //$user->notify(new ReturnTimeNotification($user,$remainTime));
            }
            if ($remainTime <= 10 && $remainTime > 5) {

                $user = User::query()->where('id', $programe->user_id)->first();
                //$user->notify(new ReturnTimeNotification($user,$remainTime));
            }
            if ($remainTime <= 5 && $remainTime > 0) {

                $user = User::query()->where('id', $programe->user_id)->first();
                //$user->notify(new ReturnTimeNotification($user,$remainTime));
            }

        }
        return 'ok';
    }

}
