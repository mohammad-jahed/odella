<?php

namespace App\Http\Controllers;

use App\Http\Requests\Time\StoreTimeRequest;
use App\Http\Requests\Time\UpdateTimeRequest;
use App\Models\Time;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class TimeController extends Controller
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

        if ($user->can('View Time')) {

            $times = Time::query()->paginate(10);

            if ($times->isEmpty()) {

                return $this->getJsonResponse(null, "There Are No Times Found!");
            }

            return $this->getJsonResponse($times, "Times Fetched Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTimeRequest $request)
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('Add Time')) {

            $data = $request->validated();

            $time = Time::query()->Create($data);

            return $this->getJsonResponse($time, "Time Created Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Time $time)
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('View Time')) {

            return $this->getJsonResponse($time, "Time Fetched Successfully");
        } else {

            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTimeRequest $request, Time $time)
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('Update Time')) {

            $data = $request->validated();

            $time->update($data);

            return $this->getJsonResponse($time, "Time Updated Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Time $time)
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('Delete Time')) {

            $time->delete();

            return $this->getJsonResponse(null, "Time Deleted Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
        }
    }
}
