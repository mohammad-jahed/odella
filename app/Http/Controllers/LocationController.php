<?php

namespace App\Http\Controllers;

use App\Http\Requests\Location\StoreLocationRequest;
use App\Http\Requests\Location\UpdateLocationRequest;
use App\Models\Location;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {

        $locations = Location::all();
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
     */
    public function show(Location $location): JsonResponse
    {

        return $this->getJsonResponse($location, "Location Fetched Successfully");
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLocationRequest $request, Location $location): JsonResponse
    {

        $data = $request->validated();
        $location->update($data);
        return $this->getJsonResponse($location, "Location Updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location): JsonResponse
    {

        $location->delete();
        return $this->getJsonResponse([], "Location Deleted Successfully");
    }
}
