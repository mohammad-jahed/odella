<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransportationLine\StoreTransportationLineRequest;
use App\Http\Requests\TransportationLine\UpdateTransportationLineRequest;
use App\Models\TransportationLine;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

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
        /**
         * @var User $user;
         */
        $user = auth()->user();
        //Gate::forUser($user)->authorize('createLine');
        if ($user->can('Add Transportation_Line')) {
            $data = $request->validated();

            $transportationLine = TransportationLine::query()->create($data);

            return $this->getJsonResponse($transportationLine, "TransportationLine Created Successfully");
        } else {
            abort(Response::HTTP_FORBIDDEN);
        }
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
        /**
         * @var User $user;
         */
        $user = auth()->user();
//        Gate::forUser($user)->authorize('updateLine');
        if ($user->can('Update Transportation_Line')) {
            $data = $request->validated();

            $transportationLine->update($data);
            return $this->getJsonResponse($transportationLine, "TransportationLine Updated Successfully");
        } else {
            abort(Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransportationLine $transportationLine): JsonResponse
    {
        /**
         * @var User $user;
         */
        $user = auth()->user();
        //Gate::forUser($user)->authorize('deleteLine');
        if ($user->can('Delete Transportation_Line')) {
            $transportationLine->delete();

            return $this->getJsonResponse([], "TransportationLine Deleted Successfully");
        } else {
            abort(Response::HTTP_FORBIDDEN);
        }
    }
}
