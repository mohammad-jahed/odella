<?php

namespace App\Http\Controllers;

use App\Http\Requests\Area\StoreAreaRequest;
use App\Http\Requests\Area\UpdateAreaRequest;
use App\Models\Area;
use App\Models\City;
use Illuminate\Http\JsonResponse;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {

        $areas = Area::all();
        return $this->getJsonResponse($areas, "Areas Fetched Successfully");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAreaRequest $request): JsonResponse
    {

        $data = $request->validated();
        $area = Area::query()->create($data);
        return $this->getJsonResponse($area, "Area Created Successfully");

    }

    /**
     * Display the specified resource.
     */
    public function show(Area $area): JsonResponse
    {

        return $this->getJsonResponse($area, "Area Fetched Successfully");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAreaRequest $request, Area $area): JsonResponse
    {
        //
        $data = $request->validated();
        $area->update($data);
        return $this->getJsonResponse($area, "Area Updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Area $area): JsonResponse
    {

        $area->delete();
        return $this->getJsonResponse([], "Area Deleted Successfully");
    }

    public function areas(City $city): JsonResponse
    {
        $areas = $city->areas;
        return $this->getJsonResponse($areas, "Areas Fetched Successfully");
    }

}
