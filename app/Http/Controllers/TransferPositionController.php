<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferPosition\StoreTransferPositionRequest;
use App\Http\Requests\TransferPosition\UpdateTransferPositionRequest;
use App\Models\TransferPosition;
use Illuminate\Http\JsonResponse;

class TransferPositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index():JsonResponse
    {
        $transferPositions = TransferPosition::all();
        return $this->getJsonResponse($transferPositions, "TransferPositions Fetch Successfully");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransferPositionRequest $request): JsonResponse
    {
        $data = $request->validated();
        $transferPosition = TransferPosition::query()->create($data);
        return $this->getJsonResponse($transferPosition, "TransferPosition Created Successfully");
    }

    /**
     * Display the specified resource.
     */
    public function show(TransferPosition $transferPosition): JsonResponse
    {
        return $this->getJsonResponse($transferPosition, "TransferPosition Fetch Successfully");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransferPositionRequest $request, TransferPosition $transferPosition): JsonResponse
    {
        $data = $request->validated();
        $transferPosition->update($data);
        return $this->getJsonResponse($transferPosition, "TransferPosition Updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransferPosition $transferPosition): JsonResponse
    {
        $transferPosition->delete();
        return $this->getJsonResponse([], "TransferPosition Deleted Successfully");
    }
}
