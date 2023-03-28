<?php

namespace App\Http\Controllers;

use App\Http\Requests\City\StoreCityRequest;
use App\Http\Requests\City\UpdateCityRequest;
use App\Models\City;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $cities = City::all();
        return $this->getJsonResponse($cities, "Cities Fetched Successfully");
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreCityRequest $request
     * @return JsonResponse
     */
    public function store(StoreCityRequest $request): JsonResponse
    {
        /**
         * @var User $user;
         */
        $user = auth()->user();
        if ($user->can('Add City')) {
            $data = $request->validated();
            $city = City::query()->create($data);
            return $this->getJsonResponse($city, "City Created Successfully");
        } else {
            abort(Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(City $city): JsonResponse
    {
        return $this->getJsonResponse($city, "City Fetched Successfully");
    }


    /**
     * Update the specified resource in storage.
     * @param UpdateCityRequest $request
     * @param City $city
     * @return JsonResponse
     */
    public function update(UpdateCityRequest $request, City $city): JsonResponse
    {
        /**
         * @var User $user;
         */
        $user = auth()->user();
        if ($user->can('Update City')) {
            $data = $request->validated();
            $city->update($data);
            return $this->getJsonResponse($city, "City Updated Successfully");
        } else {
            abort(Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param City $city
     * @return JsonResponse
     */
    public function destroy(City $city): JsonResponse
    {
        /**
         * @var User $user;
         */
        $user = auth()->user();
        if ($user->can('Delete City')) {
            $city->delete();
            return $this->getJsonResponse([], "City Deleted Successfully");
        } else {
            abort(Response::HTTP_FORBIDDEN);
        }
    }

}
