<?php

namespace App\Http\Controllers;

use App\Enums\Messages;
use App\Http\Requests\Algorithm\AlgorithmInputRequest;
use App\Models\AlgorithmInput;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AlgorithmInputController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AlgorithmInputRequest $request): JsonResponse
    {
        //
        $data = $request->validated();
        /**
         * @var AlgorithmInput $algorithmInput ;
         * @var User $auth ;
         */
        $auth = auth()->user();
        if ($auth->hasRole('Student')) {
            $response = [];
            for ($i = 0; $i < count($data['goTimes']); $i++) {

                $goTime = $data['goTimes'][$i];

                $returnTime = $data['returnTimes'][$i];

                $day_id = $data['day_ids'][$i];

                $cred = [
                    'goTime' => $goTime,
                    'returnTime' => $returnTime,
                    'day_id' => $day_id
                ];

                $algorithmInput = AlgorithmInput::query()->create($cred);

                $response[] = $algorithmInput;

                $algorithmInput->users()->attach($auth->id);
            }
            return $this->getJsonResponse($response, "Data Added Successfully!!");
        } else {
            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AlgorithmInput $algorithmInput)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AlgorithmInput $algorithmInput)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AlgorithmInput $algorithmInput)
    {
        //
    }
}
