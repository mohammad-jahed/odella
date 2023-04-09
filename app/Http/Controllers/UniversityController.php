<?php

namespace App\Http\Controllers;

use App\Http\Requests\University\StoreUniversityRequest;
use App\Http\Requests\University\UpdateUniversityRequest;
use App\Models\University;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UniversityController extends Controller
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

        if ($user->can('View Universities')) {

            $universities = University::all();

            return $this->getJsonResponse($universities, 'Universities Fetched Successfully');

        } else {

            abort(Response::HTTP_FORBIDDEN);
        }


    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUniversityRequest $request)
    {
        //
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('Add University')) {

            $data = $request->validated();

            $university = University::create($data);

            return $this->getJsonResponse($university, 'University Created Successfully');

        } else {

            abort(Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(University $university): JsonResponse
    {

        return $this->getJsonResponse($university, 'University Fetched Successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUniversityRequest $request, University $university)
    {
        //
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('Update University')) {

            $data = $request->validated();

            $university->update($data);

            return $this->getJsonResponse($university, 'University Updated Successfully');

        } else {

            abort(Response::HTTP_FORBIDDEN);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(University $university)
    {
        //
        /**
         * @var User $user ;
         */

        $user = auth()->user();

        if ($user->can('Delete University')) {

            $university->delete();

            return $this->getJsonResponse(null, 'University Deleted Successfully');

        } else {

            abort(Response::HTTP_FORBIDDEN);
        }
    }
}
