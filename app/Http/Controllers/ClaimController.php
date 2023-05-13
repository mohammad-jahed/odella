<?php

namespace App\Http\Controllers;

use App\Http\Requests\Claims\ClaimStoreRequest;
use App\Http\Requests\Claims\ClaimUpdateRequest;
use App\Http\Resources\ClaimResource;
use App\Models\Claim;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class ClaimController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        /**
         * @var User $auth ;
         */
        $auth = auth()->user();

        if ($auth->can('View Claims')) {

            $claims = Claim::with('user', 'trip')->paginate(10);

            if ($claims->isEmpty()) {

                return $this->getJsonResponse(null, "There Are No Claims Found!");
            }

            $claims = ClaimResource::collection($claims)->response()->getData(true);

            return $this->getJsonResponse($claims, "Claims Fetched Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ClaimStoreRequest $request): JsonResponse
    {
        //
        /**
         * @var User $auth
         */
        $auth = auth()->user();

        if ($auth->can('Add Claim')) {

            $data = $request->validated();

            $data['user_id'] = $auth->id;

            $claim = Claim::query()->create($data);

            $claim = new ClaimResource($claim);

            return $this->getJsonResponse($claim, 'Claim Created Successfully');

        } else {

            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
        }
    }

    /**
     * Display the specified resource.
     * @throws AuthorizationException
     */
    public function show(Claim $claim): JsonResponse
    {
        //
        /**
         * @var User $auth ;
         */
        $auth = auth()->user();

        Gate::forUser($auth)->authorize('updateClaim', $claim);
        $claim->load(['user', 'trip']);
        $claim = new ClaimResource($claim);

        return $this->getJsonResponse($claim, "Claim Fetched Successfully");
    }

    /**
     * Update the specified resource in storage.
     * @throws AuthorizationException
     */
    public function update(ClaimUpdateRequest $request, Claim $claim): JsonResponse
    {
        //
        /**
         * @var User $auth ;
         */
        $auth = auth()->user();

        Gate::forUser($auth)->authorize('updateClaim', $claim);

        $data = $request->validated();

        $claim->update($data);
        $claim->load(['user', 'trip']);
        $claim = new ClaimResource($claim);

        return $this->getJsonResponse($claim, "Claim Updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     * @throws AuthorizationException
     */
    public function destroy(Claim $claim): JsonResponse
    {
        //
        /**
         * @var User $auth ;
         */
        $auth = auth()->user();

        Gate::forUser($auth)->authorize('deleteClaim', $claim);

        $claim->delete();

        return $this->getJsonResponse(null, "Claim Deleted Successfully");

    }
}
