<?php

namespace App\Http\Controllers;

use App\Http\Requests\Bus\StoreBusRequest;
use App\Http\Requests\Bus\UpdateBusRequest;
use App\Models\Bus;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('View Buses')) {

            $buses = Bus::all();

            return $this->getJsonResponse($buses, "Buses Fetched Successfully");

        } else {

            abort(Response::HTTP_FORBIDDEN);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBusRequest $request): JsonResponse
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('Add Bus')) {

            $data = $request->validated();

            if ($request->hasFile('image')) {

                $path = $request->file('image')->store('images/buses');

                $data['image'] = $path;
            }
            $bus = Bus::query()->create($data);

            return $this->getJsonResponse($bus, "Bus Created Successfully");

        } else {

            abort(Response::HTTP_FORBIDDEN);
        }
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
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('Update Bus')) {

            $data = $request->validated();

            if ($request->hasFile('image')) {

                $path = $request->file('image')->store('images/buses');

                $data['image'] = $path;
            }
            $bus->update($data);

            return $this->getJsonResponse($bus, "Bus Updated Successfully");

        } else {

            abort(Response::HTTP_FORBIDDEN);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bus $bus): JsonResponse
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('Delete Bus')) {

            $bus->delete();

            return $this->getJsonResponse([], "Bus Deleted Successfully");

        } else {

            abort(Response::HTTP_FORBIDDEN);
        }
    }
}
