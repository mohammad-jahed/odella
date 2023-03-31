<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        //

        $programs = Program::all();
        return $this->getJsonResponse($programs,'Programs Fetched Successfully');
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

    public function userPrograms(): JsonResponse
    {
        /**
         * @var User $user;
         */
        $user = auth()->user();
        $programs = $user->programs()->with(['day','position'])->get();
        return $this->getJsonResponse($programs,'Programs Fetched Successfully');
    }


}
