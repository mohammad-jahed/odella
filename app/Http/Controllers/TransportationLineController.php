<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransportationLine\StoreTransportationLineRequest;
use App\Http\Requests\TransportationLine\UpdateTransportationLineRequest;
use App\Models\TransportationLine;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

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
     * @throws AuthorizationException
     */
    public function store(StoreTransportationLineRequest $request): JsonResponse
    {
        $user = auth()->user();
        Gate::forUser($user)->authorize('createLine');
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
     * @throws AuthorizationException
     */
    public function update(UpdateTransportationLineRequest $request, TransportationLine $transportationLine): JsonResponse
    {
        $user = auth()->user();
        Gate::forUser($user)->authorize('updateLine');
        $data = $request->validated();

        $transportationLine->update($data);
        return $this->getJsonResponse($transportationLine, "TransportationLine Updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     * @throws AuthorizationException
     */
    public function destroy(TransportationLine $transportationLine): JsonResponse
    {
        $user = auth()->user();
        Gate::forUser($user)->authorize('deleteLine');
        $transportationLine->delete();

        return $this->getJsonResponse([], "TransportationLine Deleted Successfully");
    }
}
