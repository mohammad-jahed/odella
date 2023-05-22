<?php

namespace App\Http\Controllers;

use App\Enums\Messages;
use App\Http\Requests\Lost_Founds\StoreLost_FoundsRequest;
use App\Http\Requests\Lost_Founds\UpdateLost_FoundsRequest;
use App\Http\Resources\Lost_FoundResource;
use App\Models\Lost_Found;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class LostFoundController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('View Lost&Found')) {

            $lost_founds = Lost_Found::query()->with(['user', 'trip'])->paginate(10);

            if ($lost_founds->isEmpty()) {

                return $this->getJsonResponse(null, "There Are No Lost&Founds Found!");
            }
            $lost_founds = Lost_FoundResource::collection($lost_founds)->response()->getData(true);

            return $this->getJsonResponse($lost_founds, "Lost&Founds Fetched Successfully");


        } else {

            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLost_FoundsRequest $request)
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('Add Lost&Found')) {

            $data = $request->validated();

            if ($request->hasFile('image')) {

                $path = $request->file('image')->store('images/lost_found');

                $data['image'] = $path;
            }

            $data['user_id'] = $user->id;

            $lost_found = Lost_Found::query()->create($data);

            $lost_found = new Lost_FoundResource($lost_found);

            return $this->getJsonResponse($lost_found, "Lost&Founds Created Successfully");


        } else {

            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }
    }

    /**
     * Display the specified resource.
     * @throws AuthorizationException
     */
    public function show(Lost_Found $lost_found): JsonResponse
    {
        /**
         * @var User $auth ;
         */
        $auth = auth()->user();

        Gate::forUser($auth)->authorize('viewLost&Found', $lost_found);

        $lost_found = new Lost_FoundResource($lost_found);

        return $this->getJsonResponse($lost_found, "Lost&Found Fetched Successfully");
    }

    /**
     * Update the specified resource in storage.
     * @throws AuthorizationException
     */
    public function update(UpdateLost_FoundsRequest $request, Lost_Found $lost_found): JsonResponse
    {
        //
        /**
         * @var User $auth ;
         */
        $auth = auth()->user();

        Gate::forUser($auth)->authorize('updateLost&Found', $lost_found);

        $data = $request->validated();

        if ($request->hasFile('image')) {

            $path = $request->file('image')->store('images/lost_found');

            $data['image'] = $path;
        }

        $lost_found->update($data);

        $lost_found = new Lost_FoundResource($lost_found);


        return $this->getJsonResponse($lost_found, "Lost&Found Updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     * @throws AuthorizationException
     */
    public function destroy(Lost_Found $lost_found): JsonResponse
    {
        //
        /**
         * @var User $auth ;
         */
        $auth = auth()->user();

        Gate::forUser($auth)->authorize('deleteLost&Found', $lost_found);

        $lost_found->delete();

        return $this->getJsonResponse(null, "Lost&Found Deleted Successfully");
    }

    public function getLostAndFoundsOnLoggedInSupervisorTrip()
    {
        /**
         * @var User $supervisor
         */
        $supervisor = auth()->user();

        if ($supervisor->hasRole('Supervisor')) {

            $lost_Founds = Lost_Found::query()->with(['user', 'trip'])->whereHas('trip', fn(Builder $builder) => $builder->whereHas('supervisor', fn(Builder $builder1) => $builder1->where('id', $supervisor->id)))->get();

            if ($lost_Founds->isEmpty()) {

                return $this->getJsonResponse(null, 'No Lost And Founds Found');
            }

            $lost_Founds = Lost_FoundResource::collection($lost_Founds);

            return $this->getJsonResponse($lost_Founds, "Lost And Founds Fetched Successfully");

        } else {
            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }
    }
}
