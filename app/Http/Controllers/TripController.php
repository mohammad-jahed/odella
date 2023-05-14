<?php

namespace App\Http\Controllers;

use App\Enums\TripStatus;
use App\Http\Requests\Student\StoreTripStudentsRequest;
use App\Http\Requests\Trip\StoreTripRequest;
use App\Http\Requests\Trip\UpdateTripRequest;
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

            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
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

            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
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

            if (isset($credentials['supervisor_id'])){

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

            if (! isset($credentials['supervisor_id'])){

                $dayName1 = Carbon::parse(($credentials['date'] ?? $trip->time->date))->format('l');
                /**
                 * @var Day $day1 ;
                 */
                $day1 = Day::query()->firstWhere('name_en', $dayName1);

                $newProgramData =[
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
            $trip = new TripResource($trip);

            return $this->getJsonResponse($trip, "Trip Updated Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
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

            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
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

            $data = $request->validated();

            $trip->users()->attach($data['student_ids']);

            $day = $trip->time->date->format('l');

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
                    }
                }
            }
            $trip->load('users');

            $trip = new TripResource($trip);

            return $this->getJsonResponse($trip, 'Students Added Successfully To This Trip');

        } else {

            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
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

            $trip = new TripResource($trip);

            return $this->getJsonResponse($trip, "Student Deleted Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
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

            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
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

    public function getWeeklyStudentTrips(): JsonResponse
    {
        /**
         * @var User $auth ;
         */
        $auth = auth()->user();

        $startOfWeek = now()->subWeek()->startOfWeek();

        $endOfWeek = now()->subWeek()->endOfWeek();

        $trips = $auth->trips()->with(['time', 'transferPositions', 'lines', 'busDriver'])
            ->whereHas('time', fn(Builder $builder)
            => $builder->whereBetween('date', [$startOfWeek, $endOfWeek])
                ->where('date', '<', now())
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
            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
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
            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
        }
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
            ->sendNotification('fw36VpKTIa9qWr6wuKzKSx:APA91bGEFLZw81g4tQ-BWrA3VueA3vrgF_VwsCLpCeozrGPTHB14G17sQKmIyw8p-4Zm66rhVkbEQcyZma1P4R-vZkujj9vKR21FHZcz_KKZNToJ188fq8G755oHKK8HdTr8PBfw7dDQ');
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
