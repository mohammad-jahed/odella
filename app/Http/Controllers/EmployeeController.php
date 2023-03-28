<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Http\Requests\Employee\ConfirmRegistrationRequest;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Models\Location;
use App\Models\pay;
use App\Models\payment;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use JetBrains\PhpStorm\NoReturn;
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
     * @throws AuthorizationException
     */
    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        //
        $user = auth()->user();
        //Gate::forUser($user)->authorize('createEmployee|Supervisor');
        if ($user->can('Add Employee')) {
            /**
             * @var Authenticatable $user ;
             */
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

    /**
     * @throws AuthorizationException
     */
    public function confirmRegistration(User $user, ConfirmRegistrationRequest $request): JsonResponse
    {
        $auth = auth()->user();
        //Gate::forUser($auth)->authorize('confirmRegistration');
        if ($auth->can('Confirm registration')) {
            $Credentials = $request->validated();
            $pay = pay::query()->create($Credentials);
            $user->payments()->attach($pay);
            $user->update(['status' => Status::Active]);
            $user->load('payments');
            return $this->getJsonResponse($user, "Your Register Is Confirmed Successfully");
        } else {
            abort(Response::HTTP_FORBIDDEN);
        }
    }


    public function studentsList(): JsonResponse
    {
        $user = auth()->user();
        if ($user->can('View Student')) {
            $students = User::role('Student')->get();
            return $this->getJsonResponse($students, "Students Fetch Successfully");
        } else {
            abort(Response::HTTP_FORBIDDEN);
        }
    }
}
