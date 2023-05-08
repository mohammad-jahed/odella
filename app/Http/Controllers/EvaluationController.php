<?php

namespace App\Http\Controllers;

use App\Http\Requests\Evaluations\StoreEvaluationRequest;
use App\Http\Requests\Evaluations\UpdateEvaluationRequest;
use App\Models\Evaluation;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class EvaluationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /**
         * @var User $user
         */
        $user = auth()->user();

        if ($user->can('View SupervisorEvaluation')) {

            $evaluations = Evaluation::query()->paginate(10);

            if ($evaluations->isEmpty()) {

                return $this->getJsonResponse(null, "There Are No Evaluations Found!");
            }

            return $this->getJsonResponse($evaluations, "Evaluations Fetched Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEvaluationRequest $request, Trip $trip)
    {
        /**
         * @var User $user
         */
        $user = auth()->user();

        if ($user->can('Rating Supervisor')) {

            $data = $request->validated();

            $data['user_id'] = $user->id;
            $data['trip_id'] = $trip->id;

            $evaluation = Evaluation::query()->create($data);

            return $this->getJsonResponse($evaluation, "Evaluations Created Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Evaluation $evaluation): JsonResponse
    {
        return $this->getJsonResponse($evaluation, "Evaluation Fetched Successfully");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEvaluationRequest $request, Evaluation $evaluation)
    {
        $data = $request->validated();

        $evaluation->update($data);

        return $this->getJsonResponse($evaluation, "Evaluations Updated Successfully");

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Evaluation $evaluation)
    {
        /**
         * @var User $user
         */
        $user = auth()->user();

        if ($user->can('Delete Ratting')) {

            $evaluation->delete();

            return $this->getJsonResponse(null, "Evaluations Deleted Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
        }
    }
}
