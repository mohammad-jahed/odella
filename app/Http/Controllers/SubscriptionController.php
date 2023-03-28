<?php

namespace App\Http\Controllers;

use App\Http\Requests\Subscription\StoreSubscriptionRequest;
use App\Http\Requests\Subscription\UpdateSubscriptionRequest;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
//        $user = auth()->user();
//        Gate::forUser($user)->authorize('getAllSubscriptions');
        $subscriptions = Subscription::all();
        return $this->getJsonResponse($subscriptions, "Subscriptions Fetched Successfully");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubscriptionRequest $request): JsonResponse
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();
//        Gate::forUser($user)->authorize('createSubscription');
        if ($user->can('Add Subscription')) {
            $data = $request->validated();
            $subscription = Subscription::query()->create($data);
            return $this->getJsonResponse($subscription, "Subscription Created Successfully");
        } else {
            abort(Response::HTTP_FORBIDDEN);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Subscription $subscription): JsonResponse
    {
//        $user = auth()->user();
//        Gate::forUser($user)->authorize('getSubscription');
        return $this->getJsonResponse($subscription, "Subscription Fetched Successfully");
    }

    /**
     * Update the specified resource in storage.
     * @param UpdateSubscriptionRequest $request
     * @param Subscription $subscription
     * @return JsonResponse
     */
    public function update(UpdateSubscriptionRequest $request, Subscription $subscription): JsonResponse
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();
        //Gate::forUser($user)->authorize('updateSubscription');
        if ($user->can('delete Subscription')) {
            $data = $request->validated();
            $subscription->update($data);
            return $this->getJsonResponse($subscription, "Subscription Updated Successfully");
        } else {
            abort(Response::HTTP_FORBIDDEN);
        }

    }

    /**
     * Remove the specified resource from storage.
     * @param Subscription $subscription
     * @return JsonResponse
     */
    public function destroy(Subscription $subscription): JsonResponse
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();
        //Gate::forUser($user)->authorize('deleteSubscription');
        if ($user->can('delete Subscription')) {
            $subscription->delete();
            return $this->getJsonResponse([], "Subscription Deleted Successfully");
        } else {
            abort(Response::HTTP_FORBIDDEN);
        }

    }
}
