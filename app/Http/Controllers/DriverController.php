<?php

namespace App\Http\Controllers;

use App\Http\Requests\Driver\StoreDriverRequest;
use App\Http\Requests\Driver\UpdateDriverRequest;
use App\Models\Bus;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('View Drivers')) {

            $drivers = Driver::all();

            return $this->getJsonResponse($drivers, "Drivers Fetched Successfully");

        } else {

            abort(Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDriverRequest $request): JsonResponse
    {
        /**
         * @var Driver $driver ;
         * @var Bus $bus ;
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('Add Driver')) {

            $data = $request->validated();

            $driver = Driver::query()->create($data);

            $driver->buses()->attach($data['bus_ids']);

            return $this->getJsonResponse($driver, "Driver Created Successfully");

        } else {

            abort(Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Driver $driver): JsonResponse
    {
        return $this->getJsonResponse($driver, "Driver Fetched Successfully");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDriverRequest $request, Driver $driver): JsonResponse
    {
        /**
         * @var Driver $driver ;
         * @var Bus $bus ;
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('Update Driver')) {

            $data = $request->validated();

            if (isset($data['bus_ids'])) {

                $driver->buses()->sync($data['bus_ids']);
            }
            $driver->update($data);

            return $this->getJsonResponse($driver, "Driver Updated Successfully");

        } else {

            abort(Response::HTTP_FORBIDDEN);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Driver $driver): JsonResponse
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('Delete Driver')) {

            $driver->delete();

            return $this->getJsonResponse(null, "Driver Deleted Successfully");

        } else {

            abort(Response::HTTP_FORBIDDEN);
        }
    }
}
