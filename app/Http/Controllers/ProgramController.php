<?php

namespace App\Http\Controllers;

use App\Enums\Messages;
use App\Http\Resources\ProgramResource;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('View Programs')) {

            $programs = Program::query()->paginate(10);

            if ($programs->isEmpty()) {

                return $this->getJsonResponse(null, "There Are No Programs Found!");
            }

            $programs = ProgramResource::collection($programs)->response()->getData(true);

            return $this->getJsonResponse($programs, 'Programs Fetched Successfully');

        } else {

            abort(Response::HTTP_UNAUTHORIZED, Messages::UNAUTHORIZED);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Program $program)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Program $program)
    {
        //

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Program $program)
    {
        //
    }

    /**
     * Retrieves a list of programs associated with the current user.
     */
    public function userPrograms(): JsonResponse
    {
        /**
         * @var User $user ;
         * @var Program $program
         */
        $user = auth()->user();

        $programs = $user->programs()->with(['day', 'position'])->get();

        if ($programs->isEmpty()) {

            return $this->getJsonResponse(null, "There Are No Programs Found For This User!");
        }
        $programs = ProgramResource::collection($programs);

        return $this->getJsonResponse($programs, 'Programs Fetched Successfully');
    }


}
