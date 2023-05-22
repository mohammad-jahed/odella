<?php

namespace App\Http\Controllers;

use App\Http\Requests\Location\StoreLocationRequest;
use App\Http\Requests\Location\UpdateLocationRequest;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     * @throws AuthorizationException
     */
    public function index(): JsonResponse
    {
        $user = auth()->user();

        Gate::forUser($user)->authorize('getAllLocations');

        $locations = Location::query()->with(['city', 'area'])
            ->paginate(10);

        if ($locations->isEmpty()) {

            return $this->getJsonResponse(null, "There Are No Locations Found!");
        }

        $locations = LocationResource::collection($locations)->response()->getData(true);

        return $this->getJsonResponse($locations, "Locations Fetched Successfully");

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLocationRequest $request): JsonResponse
    {

        $data = $request->validated();

        $location = Location::query()->create($data);

        return $this->getJsonResponse($location, "Location Created Successfully");

    }

    /**
     * Display the specified resource.
     * @throws AuthorizationException
     */
    public function show(Location $location): JsonResponse
    {
        $user = auth()->user();

        Gate::forUser($user)->authorize('getLocation', $location);

        $location->load(['city', 'area']);

        $location = new LocationResource($location);

        return $this->getJsonResponse($location, "Location Fetched Successfully");

    }


    /**
     * Update the specified resource in storage.
     * @throws AuthorizationException
     */
    public function update(UpdateLocationRequest $request, Location $location): JsonResponse
    {
        $user = auth()->user();

        Gate::forUser($user)->authorize('updateLocation', $location);

        $data = $request->validated();

        $location->update($data);

        return $this->getJsonResponse($location, "Location Updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     * @throws AuthorizationException
     */
    public function destroy(Location $location): JsonResponse
    {
        $user = auth()->user();

        Gate::forUser($user)->authorize('deleteLocation', $location);

        $location->delete();

        return $this->getJsonResponse(null, "Location Deleted Successfully");
    }
}
