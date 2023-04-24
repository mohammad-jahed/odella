<?php

namespace App\Http\Controllers;

use App\Models\Day;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DaysController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $days = Day::all();

        return $this->getJsonResponse($days, "Days Fetched Successfully");
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
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
