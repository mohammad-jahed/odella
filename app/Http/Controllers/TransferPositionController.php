<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferPosition\StoreTransferPositionRequest;
use App\Http\Requests\TransferPosition\UpdateTransferPositionRequest;
use App\Models\TransferPosition;
use App\Models\TransportationLine;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class TransferPositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $transferPositions = TransferPosition::all();
        return $this->getJsonResponse($transferPositions, "TransferPositions Fetched Successfully");
    }

    /**
     * Store a newly created resource in storage.
     * @throws AuthorizationException
     */
    public function store(StoreTransferPositionRequest $request): JsonResponse
    {
        $user = auth()->user();
        //Gate::forUser($user)->authorize('createPosition');
        if ($user->can('Add Position')) {
            $data = $request->validated();
            $transferPosition = TransferPosition::query()->create($data);
            /**
             * @var TransferPosition $transferPosition ;
             * @var TransportationLine $line ;
             */

            $line = TransportationLine::query()->where('id', $data['line_id'])->first();
            $transferPosition->lines()->attach($line->id);
            return $this->getJsonResponse($transferPosition, "TransferPosition Created Successfully");
        } else {
            abort(Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TransferPosition $transferPosition): JsonResponse
    {
        return $this->getJsonResponse($transferPosition, "TransferPosition Fetched Successfully");
    }

    /**
     * Update the specified resource in storage.
     * @throws AuthorizationException
     */
    public function update(UpdateTransferPositionRequest $request, TransferPosition $transferPosition): JsonResponse
    {
        $user = auth()->user();
        //Gate::forUser($user)->authorize('updatePosition');
        if ($user->can('Update Position')) {
            $data = $request->validated();
            $transferPosition->update($data);
            if (isset($data['line_id'])) {
                /**
                 * @var TransportationLine $line ;
                 */
                $line = TransportationLine::query()->where('id', $data['line_id'])->first();
                $transferPosition->lines()->sync([$line->id]);
            }
            return $this->getJsonResponse($transferPosition, "TransferPosition Updated Successfully");
        } else {
            abort(Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @throws AuthorizationException
     */
    public function destroy(TransferPosition $transferPosition): JsonResponse
    {
        $user = auth()->user();
        //Gate::forUser($user)->authorize('deletePosition');
        if ($user->can('Delete Position')) {
            $transferPosition->delete();
            return $this->getJsonResponse([], "TransferPosition Deleted Successfully");
        } else {
            abort(Response::HTTP_FORBIDDEN);
        }
    }

    public function positions(TransportationLine $line): JsonResponse
    {
        $positions = $line->positions;
        return $this->getJsonResponse($positions, "Position Fetched Successfully");
    }
}
