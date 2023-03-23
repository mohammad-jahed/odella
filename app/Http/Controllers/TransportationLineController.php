<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransportationLine\StoreTransportationLineRequest;
use App\Http\Requests\TransportationLine\UpdateTransportationLineRequest;
use App\Models\TransportationLine;
use Illuminate\Http\JsonResponse;

class TransportationLineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $transportationLines = TransportationLine::all();

        return $this->getJsonResponse($transportationLines, "TransportationLines Fetch Successfully");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransportationLineRequest $request): JsonResponse
    {
        $data = $request->validated();

        $transportationLine = TransportationLine::query()->create($data);

        return $this->getJsonResponse($transportationLine, "TransportationLine Created Successfully");
    }

    /**
     * Display the specified resource.
     */
    public function show(TransportationLine $transportationLine): JsonResponse
    {

        return $this->getJsonResponse($transportationLine, "TransportationLine Fetch Successfully");

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransportationLineRequest $request, TransportationLine $transportationLine): JsonResponse
    {
        $data = $request->validated();

        $transportationLine->update($data);
        return $this->getJsonResponse($transportationLine, "TransportationLine Updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransportationLine $transportationLine): JsonResponse
    {
        $transportationLine->delete();

        return $this->getJsonResponse([], "TransportationLine Deleted Successfully");
    }
}
