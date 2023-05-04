<?php

namespace App\Http\Controllers;

use App\Http\Requests\Lost_Founds\StoreLost_FoundsRequest;
use App\Http\Requests\Lost_Founds\UpdateLost_FoundsRequest;
use App\Models\Lost_Found;
use App\Models\User;
use Illuminate\Http\JsonResponse;
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

            $lost_founds = Lost_Found::query()->paginate(10);

            if ($lost_founds->isEmpty()) {

                return $this->getJsonResponse(null, "There Are No Lost&Founds Found!");
            }

            return $this->getJsonResponse($lost_founds, "Lost&Founds Fetched Successfully");


        } else {

            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
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

            return $this->getJsonResponse($lost_found, "Lost&Founds Created Successfully");


        } else {

            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Lost_Found $lost_Found)
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('View Lost&Found')) {

            return $this->getJsonResponse($lost_Found, "Lost&Found Fetched Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLost_FoundsRequest $request, Lost_Found $lost_Found): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {

            $path = $request->file('image')->store('images/lost_found');

            $data['image'] = $path;
        }

        $lost_Found->update($data);

        return $this->getJsonResponse($lost_Found, "Lost&Found Updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lost_Found $lost_Found): JsonResponse
    {
        $lost_Found->delete();

        return $this->getJsonResponse(null, "Lost&Found Deleted Successfully");
    }
}
