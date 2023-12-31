<?php

namespace App\Http\Controllers;

use App\Enums\GuestStatus;
use App\Enums\Messages;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends Controller
{
    //

    public function studentsByDayAndUniversity(): JsonResponse
    {
        /**
         * @var User $auth ;
         */
        $auth = auth()->user();
        if ($auth->hasRole('Admin')) {

            $usersAndTrips = User::role('Student')
                ->join('universities', 'users.university_id', '=', 'universities.id')
                ->join('trip_users', 'users.id', '=', 'trip_users.user_id')
                ->join('trips', 'trip_users.trip_id', '=', 'trips.id')
                ->join('times', 'trips.time_id', '=', 'times.id')
                ->select('universities.name_en as university', 'times.day as day',
                    DB::raw('count(distinct users.id) as num_users')
                )
                ->groupBy('universities.name_en', 'times.day')
                ->orderBy('universities.name_en', 'asc')
                ->orderBy('times.day', 'asc')
                ->get();

            return $this->getJsonResponse($usersAndTrips, 'Data Fetched Successfully');

        } else {
            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }
    }


    public function tripsByDayAndUniversities(): JsonResponse
    {
        /**
         * @var User $auth ;
         */
        $auth = auth()->user();
        if ($auth->hasRole('Admin')) {

            $trips = User::role('Student')
                ->join('universities', 'users.university_id', '=', 'universities.id')
                ->join('trip_users', 'users.id', '=', 'trip_users.user_id')
                ->join('trips', 'trip_users.trip_id', '=', 'trips.id')
                ->join('times', 'trips.time_id', '=', 'times.id')
                ->select('universities.name_en as university', 'times.day as day',
                    DB::raw('count(distinct trips.id) as num_trips'))
                ->groupBy('universities.name_en', 'times.day')
                ->orderBy('universities.name_en', 'asc')
                ->orderBy('times.day', 'asc')
                ->get();

            return $this->getJsonResponse($trips, 'Data Fetched Successfully');

        } else {
            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }
    }


    public function studentsByLine()
    {
        /**
         * @var User $auth ;
         */
        $auth = auth()->user();

        if ($auth->hasRole('Admin')) {

            $users = User::query()
                ->join('universities', 'users.university_id', '=', 'universities.id')
                ->join('trip_users', 'users.id', '=', 'trip_users.user_id')
                ->join('trips', 'trip_users.trip_id', '=', 'trips.id')
                ->join('trip_lines', 'trips.id', '=', 'trip_lines.trip_id')
                ->join('transportation_lines', 'transportation_lines.id', '=', 'trip_lines.line_id')
                ->select('universities.name_en as university', 'transportation_lines.name_en as name',
                    DB::raw('count(distinct users.id) as num_users'))
                ->groupBy('universities.name_en', 'transportation_lines.name_en')
                ->orderBy('universities.name_en')
                ->orderBy('transportation_lines.name_en')
                ->get();

            return $this->getJsonResponse($users, 'Data Fetched Successfully');

        } else {
            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }

    }

    public function tripClaimStatistics()
    {
        /**
         * @var User $auth ;
         */
        $auth = auth()->user();

        if ($auth->hasRole('Admin')) {

            $tripClaims = Trip::query()->with(['time', 'claims' => function ($query) {
                $query->selectRaw('count(*) as claim_count, trip_id')->groupBy('trip_id');
            }])->get();


            $result = $tripClaims->map(function ($trip) {
                return [
                    'time' => $trip->time->start,
                    'claims_count' => $trip->claims->isEmpty() ? 0 : $trip->claims->first()->claim_count,
                ];
            });

            return $this->getJsonResponse($result, 'Data Fetched Successfully');

        } else {
            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }
    }

    public function tripEvaluationsStatistics(): JsonResponse
    {

        /**
         * @var User $auth ;
         */
        $auth = auth()->user();

        if (!$auth->hasRole('Admin')) {
            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }
        $tripEvaluations = Trip::query()->with(['time', 'evaluations' => function ($query) {
            $query->selectRaw('count(*) as evaluation_count, trip_id')->groupBy('trip_id');
        }])->get();


        $result = $tripEvaluations->map(function ($trip) {
            return [
                'time' => $trip->time->start,
                'evaluations_count' => $trip->evaluations->isEmpty() ? 0 : $trip->evaluations->first()->evaluation_count,
            ];
        });

        return $this->getJsonResponse($result, 'Data Fetched Successfully');

    }

    public function dailyReservationStatistics(): JsonResponse
    {
        /**
         * @var User $auth ;
         */
        $auth = auth()->user();

        if (!$auth->hasRole('Admin')) {
            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }

        $tripDailyReservations = Trip::query()->with(['time',
            'dailyReservations' => function ($query) {
                $query->selectRaw('count(*) as reservations_count, trip_id')->where('guestRequestStatus', '=', GuestStatus::Approved)->groupBy('trip_id');
            }
        ])->get();

        $result = $tripDailyReservations->map(function ($trip) {
            return [
                'time' => $trip->time->start,
                'reservations_count' => $trip->dailyReservations->isEmpty() ? 0 : $trip->dailyReservations->first()->reservations_count,
            ];
        });

        return $this->getJsonResponse($result, 'Data Fetched Successfully');

    }
}
