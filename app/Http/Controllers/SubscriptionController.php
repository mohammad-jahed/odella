<?php

namespace App\Http\Controllers;

use App\Http\Requests\Subscription\StoreSubscriptionRequest;
use App\Http\Requests\Subscription\UpdateSubscriptionRequest;
use App\Models\Subscription;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     * @throws AuthorizationException
     */
    public function index(): JsonResponse
    {
        $user = auth()->user();
        Gate::forUser($user)->authorize('getAllSubscriptions');
        $subscriptions = Subscription::all();
        return $this->getJsonResponse($subscriptions, "Subscriptions Fetched Successfully");
    }

    /**
     * Store a newly created resource in storage.
     * @throws AuthorizationException
     */
    public function store(StoreSubscriptionRequest $request): JsonResponse
    {
        $user = auth()->user();
        Gate::forUser($user)->authorize('createSubscription');
        $data = $request->validated();
        $subscription = Subscription::query()->create($data);
        return $this->getJsonResponse($subscription, "Subscription Created Successfully");
    }

    /**
     * Display the specified resource.
     * @throws AuthorizationException
     */
    public function show(Subscription $subscription): JsonResponse
    {
        $user = auth()->user();
        Gate::forUser($user)->authorize('getSubscription');
        return $this->getJsonResponse($subscription, "Subscription Fetched Successfully");
    }

    /**
     * Update the specified resource in storage.
     * @throws AuthorizationException
     */
    public function update(UpdateSubscriptionRequest $request, Subscription $subscription): JsonResponse
    {
        $user = auth()->user();
        Gate::forUser($user)->authorize('updateSubscription');
        $data = $request->validated();
        $subscription->update($data);
        return $this->getJsonResponse($subscription, "Subscription Updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     * @throws AuthorizationException
     */
    public function destroy(Subscription $subscription): JsonResponse
    {
        $user = auth()->user();
        Gate::forUser($user)->authorize('deleteSubscription');
        $subscription->delete();
        return $this->getJsonResponse([], "Subscription Deleted Successfully");
    }
}
