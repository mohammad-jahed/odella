<?php

namespace App\Http\Controllers;

use App\Enums\Messages;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
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
                ->orderBy('universities.name_en', 'asc')
                ->orderBy('transportation_lines.name_en', 'asc')
                ->get();

            return $this->getJsonResponse($users, 'Data Fetched Successfully');

        } else {
            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }


    }
}
