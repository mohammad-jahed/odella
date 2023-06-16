<?php

namespace App\Http\Controllers;

use App\Enums\Messages;
use App\Enums\TripStatus;
use App\Http\Requests\Student\StoreTripStudentsRequest;
use App\Http\Requests\Supervisor\SupervisorTripRequest;
use App\Http\Requests\Trip\StoreTripRequest;
use App\Http\Requests\Trip\UpdateTripRequest;
use App\Http\Requests\Trips\GenerateTripsRequest;
use App\Http\Resources\EvaluationResource;
use App\Http\Resources\TripResource;
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
                $credentials['status'] = ($credentials['start'] > "07:00" && $credentials['start'] < "11:00") ? TripStatus::GoTrip : TripStatus::ReturnTrip;

                /**
                 * @var Time $time ;
                 * @var Trip $trip ;
                 * @var TransportationLine $line ;
                 */
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

                    $data += ['date' => $credentials['date']];
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

        $today = Carbon::parse(Carbon::now()); // Replace with your original date
        $beforeWeek = $today->copy()->subDays(7);

        $trips = $auth->trips()->with(['time', 'busDriver'])
            ->whereHas('time', fn(Builder $builder) => $builder->whereBetween('date', [$beforeWeek, $today])
            )->get();

        $trips = TripResource::collection($trips);

        return $this->getJsonResponse($trips, "Trips Fetched Successfully");

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
            new \DateTime('01:00'),
            new \DateTime('01:30'),
            new \DateTime('02:00'),
            new \DateTime('02:30'),
            new \DateTime('03:00'),
        ];
        $goTripsResponse = [];
        foreach ($initialGoTrips as $goTimeTrip) {
            $goTimeTrip = $goTimeTrip->format('h:i A');
            $usersNumber = User::query()->whereHas('algorithm_inputs',
                fn(Builder $builder) => $builder->whereTime('goTime', $goTimeTrip)
            )->count();
            $goTripsResponse[$goTimeTrip] = $usersNumber;
        }

        sort($goTripsResponse);
        for ($i = sizeof($goTripsResponse) - 1; $i > sizeof($goTripsResponse) - $data['goTripsNumber']; $i--) {
            $goTripsResponse[$i - $data['goTripsNumber']] += $goTripsResponse[$i];
        }
        $nGoTrip = array_slice($goTripsResponse, 0, $data['goTripsNumber']);

        $returnTripsResponse = [];
        foreach ($initialReturnTrips as $returnTimeTrip) {
            $returnTimeTrip = $returnTimeTrip->format('h:i P');
            $usersNumber = User::query()->whereHas('algorithm_inputs',
                fn(Builder $builder) => $builder->whereTime('returnTime', $returnTimeTrip)
            )->count();
            $returnTripsResponse[$returnTimeTrip] = $usersNumber;
        }
        for ($i = sizeof($returnTripsResponse) - 1; $i > sizeof($returnTripsResponse) - $data['returnTripsNumber']; $i--) {
            $returnTripsResponse[$i - $data['goTripsNumber']] += $returnTripsResponse[$i];
        }
        sort($returnTripsResponse);
        $mReturnTrip = array_slice($returnTripsResponse, 0, $data['returnTripsNumber']);

        $response = [
            'goTrips' => $nGoTrip,
            'returnTrips' => $mReturnTrip
        ];

        return $this->getJsonResponse($response, "Suggestions for trip generation created successfully");
    }


    public function sendNotification()
    {
        return Larafirebase::withTitle('Test Title')
            ->withBody('Test body')
            ->withSound('default')
            ->withPriority('high')
            ->withAdditionalData([
                'color' => '#rrggbb',
                'badge' => 0,
            ])
            ->sendNotification('fJp-Q8srT8KlYP-okTvNqy:APA91bFSd5bTw4DbmyQoeevsLkiaISgBQ_gJF8tz9qKeAq_wwj_6seErja-VQdOsjiDXvCr2fbSfzVuyya55qirsrEG9gNDmAAOFEbiI-Tnlis2EAVRlZ-Udqb957kgb5GUw0Vhpr4dv');
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
