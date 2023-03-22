<?php

namespace App\Http\Controllers;

use App\Http\Requests\Subscription\StoreSubscriptionRequest;
use App\Http\Requests\Subscription\UpdateSubscriptionRequest;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        //
        $subscriptions = Subscription::all();
        return $this->getJsonResponse($subscriptions,"Subscriptions Fetched Successfully");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubscriptionRequest $request): JsonResponse
    {
        //
        $data = $request->validated();
        $subscription = Subscription::query()->create($data);
        return $this->getJsonResponse($subscription,"Subscription Created Successfully");
    }

    /**
     * Display the specified resource.
     */
    public function show(Subscription $subscription): JsonResponse
    {
        //
        return $this->getJsonResponse($subscription, "Subscription Fetched Successfully");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubscriptionRequest $request, Subscription $subscription): JsonResponse
    {
        //
        $data = $request->validated();
        $subscription->update($data);
        return $this->getJsonResponse($subscription,"Subscription Updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subscription $subscription): JsonResponse
    {
        //
        $subscription->delete();
        return $this->getJsonResponse([],"Subscription Deleted Successfully");
    }
}
