<?php

namespace App\Http\Controllers;

use App\Http\Requests\Bus\StorBusRequest;
use App\Http\Requests\Bus\UpdateBusRequest;
use App\Models\Bus;
use Illuminate\Http\JsonResponse;

class BusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $buses = Bus::all();
        return $this->getJsonResponse($buses, "Buses Fetched Successfully");
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StorBusRequest $request): JsonResponse
    {
        $data = $request->validated();
        $bus = Bus::query()->create($data);
        return $this->getJsonResponse($bus, "Bus Created Successfully");
    }

    /**
     * Display the specified resource.
     */
    public function show(Bus $bus): JsonResponse
    {
        return $this->getJsonResponse($bus, "Bus Fetched Successfully");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBusRequest $request, Bus $bus): JsonResponse
    {
        $data = $request->validated();
        $bus->update($data);
        return $this->getJsonResponse($bus, "Bus Updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bus $bus): JsonResponse
    {
        $bus->delete();
        return $this->getJsonResponse([], "Bus Deleted Successfully");
    }
}
