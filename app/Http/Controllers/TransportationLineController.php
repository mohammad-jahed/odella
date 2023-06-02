<?php

namespace App\Http\Controllers;

use App\Enums\Messages;
use App\Http\Requests\TransportationLine\StoreTransportationLineRequest;
use App\Http\Requests\TransportationLine\UpdateTransportationLineRequest;
use App\Http\Resources\TransportationLineResource;
use App\Models\TransferPosition;
use App\Models\TransportationLine;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TransportationLineController extends Controller
{
    /**
     * @var TransportationLine[] $transportationLines ;
     * @var TransportationLine $transportationLine ;
     * @var TransferPosition[] $positions ;
     */

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {

        $transportationLines = TransportationLine::query()->paginate(10);

        if ($transportationLines->isEmpty()) {

            return $this->getJsonResponse(null, "There Are No TransportationLines Found!");
        }
        $transportationLines = TransportationLineResource::collection($transportationLines)->response()->getData(true);
        return $this->getJsonResponse($transportationLines, "TransportationLines Fetch Successfully");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransportationLineRequest $request): JsonResponse
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('Add Transportation_Line')) {

            $data = $request->validated();

            $transportationLine = TransportationLine::query()->create($data);

            return $this->getJsonResponse($transportationLine, "TransportationLine Created Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TransportationLine $transportationLine): JsonResponse
    {
        $transportationLine = new TransportationLineResource($transportationLine);
        return $this->getJsonResponse($transportationLine, "TransportationLine Fetch Successfully");

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransportationLineRequest $request, TransportationLine $transportationLine): JsonResponse
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('Update Transportation_Line')) {

            $data = $request->validated();

            $transportationLine->update($data);
            $transportationLine = new TransportationLineResource($transportationLine);
            return $this->getJsonResponse($transportationLine, "TransportationLine Updated Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransportationLine $transportationLine): JsonResponse
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('Delete Transportation_Line')) {

            $transportationLine->delete();

            return $this->getJsonResponse(null, "TransportationLine Deleted Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }
    }
}
