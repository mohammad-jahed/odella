<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferPosition\StoreTransferPositionRequest;
use App\Http\Requests\TransferPosition\UpdateTransferPositionRequest;
use App\Models\TransferPosition;
use App\Models\TransportationLine;
use App\Models\User;
use Illuminate\Http\JsonResponse;
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
     */
    public function store(StoreTransferPositionRequest $request): JsonResponse
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

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
     */
    public function update(UpdateTransferPositionRequest $request, TransferPosition $transferPosition): JsonResponse
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

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
     */
    public function destroy(TransferPosition $transferPosition): JsonResponse
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('Delete Position')) {

            $transferPosition->delete();

            return $this->getJsonResponse(null, "TransferPosition Deleted Successfully");

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
