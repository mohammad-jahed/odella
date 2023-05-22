<?php

namespace App\Http\Controllers;

use App\Enums\Messages;
use App\Http\Requests\Evaluations\StoreEvaluationRequest;
use App\Http\Requests\Evaluations\UpdateEvaluationRequest;
use App\Http\Resources\EvaluationResource;
use App\Models\Evaluation;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
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

            $evaluations = EvaluationResource::collection($evaluations)->response()->getData(true);

            return $this->getJsonResponse($evaluations, "Evaluations Fetched Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
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

            $evaluation = new EvaluationResource($evaluation);

            return $this->getJsonResponse($evaluation, "Evaluations Created Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }
    }

    /**
     * Display the specified resource.
     * @throws AuthorizationException
     */
    public function show(Evaluation $evaluation): JsonResponse
    {
        /**
         * @var User $auth ;
         */
        $auth = auth()->user();

        Gate::forUser($auth)->authorize('viewEvaluation', $evaluation);

        $evaluation = new EvaluationResource($evaluation);

        return $this->getJsonResponse($evaluation, "Evaluation Fetched Successfully");
    }

    /**
     * Update the specified resource in storage.
     * @throws AuthorizationException
     */
    public function update(UpdateEvaluationRequest $request, Evaluation $evaluation): JsonResponse
    {
        /**
         * @var User $auth ;
         */
        $auth = auth()->user();

        $data = $request->validated();

        Gate::forUser($auth)->authorize('updateEvaluation', $evaluation);

        $evaluation->update($data);

        $evaluation = new EvaluationResource($evaluation);

        return $this->getJsonResponse($evaluation, "Evaluations Updated Successfully");

    }

    /**
     * Remove the specified resource from storage.
     * @throws AuthorizationException
     */
    public function destroy(Evaluation $evaluation): JsonResponse
    {
        /**
         * @var User $user
         */
        $user = auth()->user();

        Gate::forUser($user)->authorize('deleteEvaluation', $evaluation);

        $evaluation->delete();

        return $this->getJsonResponse(null, "Evaluations Deleted Successfully");
    }

    /**
     * Get all Evaluations for a specific trip.
     */
    public function trip_evaluations(Trip $trip)
    {
        /**
         * @var User $user
         */
        $user = auth()->user();

        if ($user->can('View SupervisorEvaluation')) {

            $evaluations = $trip->evaluations;

            $evaluations = EvaluationResource::collection($evaluations);

            return $this->getJsonResponse($evaluations, "Evaluations Fetched Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }
    }
}
