<?php

namespace App\Http\Controllers;

use App\Http\Requests\Driver\StorDriverRequest;
use App\Http\Requests\Driver\UpdateDriverRequest;
use App\Models\Driver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $drivers = Driver::all();
        return $this->getJsonResponse($drivers, "Drivers Fetched Successfully");
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StorDriverRequest $request): JsonResponse
    {
        $data = $request->validated();
        $driver = Driver::query()->create($data);
        return $this->getJsonResponse($driver, "Driver Created Successfully");
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
        $data = $request->validated();
        $driver->update($data);
        return $this->getJsonResponse($driver, "Driver Updated Successfully");

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Driver $driver): JsonResponse
    {
        $driver->delete();
        return $this->getJsonResponse([], "Driver Deleted Successfully");
    }
}
