<?php

namespace App\Http\Controllers;

use App\Http\Requests\City\StoreCityRequest;
use App\Http\Requests\City\UpdateCityRequest;
use App\Models\City;
use Illuminate\Http\JsonResponse;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        //

        $cities = City::all();
        return $this->getJsonResponse($cities, "Cities Fetched Successfully");
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCityRequest $request): JsonResponse
    {
        //
        $data = $request->validated();
        $city = City::query()->create($data);
        return $this->getJsonResponse($city,"City Created Successfully");
    }

    /**
     * Display the specified resource.
     */
    public function show(City $city): JsonResponse
    {
        //
        return $this->getJsonResponse($city,"City Fetched Successfully");
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCityRequest $request, City $city): JsonResponse
    {
        //
        $data = $request->validated();
        $city->update($data);
        return $this->getJsonResponse($city,"City Updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(City $city): JsonResponse
    {
        //
        $city->delete();
        return $this->getJsonResponse([],"City Deleted Successfully");
    }

}
