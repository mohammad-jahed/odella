<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Http\Requests\Supervisor\StoreSupervisorRequest;
use App\Models\Location;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;

class SupervisorController extends Controller
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
     * @throws AuthorizationException
     */
    public function store(StoreSupervisorRequest $request): JsonResponse
    {
        //
        $user = auth()->user();
        //Gate::forUser($user)->authorize('createEmployee|Supervisor');
        if ($user->can('Add Supervisor')) {
            /**
             * @var Authenticatable $user ;
             */
            $credentials = $request->validated();
            $credentials['password'] = Hash::make($credentials['password']);
            /**
             * @var Location $location ;
             */
            $location = Location::query()->create($credentials);
            $credentials['location_id'] = $location->id;
            $credentials['status'] = Status::NonStudents;
            $user = User::query()->create($credentials);
            $role = Role::query()->where('name', 'like', 'Supervisor')->get();
            $user->assignRole($role);
            return $this->getJsonResponse($user, "Supervisor Registered Successfully");
        } else {
            abort(Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
