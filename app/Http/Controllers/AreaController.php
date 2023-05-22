<?php

namespace App\Http\Controllers;

use App\Enums\Messages;
use App\Http\Requests\Area\StoreAreaRequest;
use App\Http\Requests\Area\UpdateAreaRequest;
use App\Http\Resources\AreaResource;
use App\Models\Area;
use App\Models\City;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {

        $areas = Area::query()->paginate(10);

        if ($areas->isEmpty()) {

            return $this->getJsonResponse(null, "There Are No Areas Found!");
        }

        $areas = AreaResource::collection($areas);

        return $this->getJsonResponse($areas, "Areas Fetched Successfully");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAreaRequest $request): JsonResponse
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('Add Area')) {

            $data = $request->validated();

            $area = Area::query()->create($data);

            return $this->getJsonResponse($area, "Area Created Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }

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
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('Update Area')) {

            $data = $request->validated();

            $area->update($data);

            return $this->getJsonResponse($area, "Area Updated Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param Area $area
     * @return JsonResponse
     */
    public function destroy(Area $area): JsonResponse
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('Delete Area')) {

            $area->delete();

            return $this->getJsonResponse(null, "Area Deleted Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }
    }

    /**
     * Get all areas for a specific city.
     */
    public function areas(City $city): JsonResponse
    {
        $areas = $city->areas;

        $areas = AreaResource::collection($areas);

        return $this->getJsonResponse($areas, "Areas Fetched Successfully");
    }

}
