<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransportationLine\StoreTransportationLineRequest;
use App\Http\Requests\TransportationLine\UpdateTransportationLineRequest;
use App\Models\TransferPosition;
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
        /**
         * @var TransportationLine[] $transportationLines ;
         * @var TransportationLine $transportationLine ;
         * @var TransferPosition[] $positions ;
         */
        $transportationLines = TransportationLine::all();
//        foreach ($transportationLines as $transportationLine) {
//            $positions = [$transportationLine->positions];
//            $transportationLine += [
//                'from' => $positions[0],
//                'to' => $positions[sizeof($positions) - 1]
//            ];
//            $transportationLines->add($transportationLine);
//        }
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
         * @var User $user ;
         */
        $user = auth()->user();

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
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('Delete Transportation_Line')) {

            $transportationLine->delete();

            return $this->getJsonResponse([], "TransportationLine Deleted Successfully");

        } else {

            abort(Response::HTTP_FORBIDDEN);
        }
    }
}
