<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Http\Requests\Employee\ConfirmRegistrationRequest;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Models\Location;
use App\Models\Pay;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;

class EmployeeController extends Controller
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
    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        //
        /**
         * @var User $auth;
         */
        $auth = auth()->user();
        if ($auth->can('Add Employee')) {
            $credentials = $request->validated();
            $credentials['password'] = Hash::make($credentials['password']);
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('images/employee');
                $credentials['image'] = $path;
            }
            /**
             * @var Location $location ;
             */
            $location = Location::query()->create($credentials);
            $credentials['location_id'] = $location->id;
            $credentials['status'] = Status::NonStudents;
            /**
             * @var User $user;
             */
            $user = User::query()->create($credentials);
            $role = Role::query()->where('name', 'like', 'Employee')->first();
            $user->assignRole($role);
            return $this->getJsonResponse($user, "Employee Registered Successfully");
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
     * @throws AuthorizationException
     */
    public function update(UpdateEmployeeRequest $request, User $employee): JsonResponse
    {
        //
        /**
         * @var User $auth;
         */

        $auth = auth()->user();
        Gate::forUser($auth)->authorize('updateProfile',$employee);

        $credentials = $request->validated();
        if(isset($credentials['password'])){
            $credentials['password'] = Hash::make($credentials['password']);
        }
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images/employee');
            $credentials['image'] = $path;
        }
        if (isset($credentials['city_id']) || isset($credentials['area_id']) || isset($credentials['street'])) {
            /**
             * @var Location $location ;
             */
            $location = Location::query()->update($credentials);
            $credentials['location_id'] = $location->id;
        }
        $employee->update($credentials);
        return $this->getJsonResponse($employee, "Employee Updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }

    /**
     */
    public function confirmRegistration(User $user, ConfirmRegistrationRequest $request): JsonResponse
    {
        /**
         * @var User $auth;
         */
        $auth = auth()->user();

        if ($auth->can('Confirm registration')) {
            $Credentials = $request->validated();
            $pay = Pay::query()->create($Credentials);
            $user->payments()->attach($pay);
            $user->update(['status' => Status::Active]);
            $user->load('payments');
            return $this->getJsonResponse($user, "Your Register Is Confirmed Successfully");
        } else {
            abort(Response::HTTP_FORBIDDEN);
        }
    }



}
