<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Employee\EmployeeRegisterRequest;
use App\Http\Requests\Supervisor\StoreSupervisorRequest;
use App\Models\Location;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'adminRegister']]);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (!$token = auth('api')->attempt($data)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        /**
         * @var User $user ;
         */
        $user = auth()->user();
        if ($user->status == Status::UnActive) {
            return $this->getJsonResponse($user, "Un authorized, Please visit the Company Office to Complete Registration Process");
        }

        return $this->createNewToken($token);
    }


    public function register(RegisterRequest $request): JsonResponse
    {
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
        $user = User::query()->create($credentials);

        $role = Role::query()->where('name', 'like', 'Student')->get();
        $user->assignRole($role);
        return $this->getJsonResponse($user, "User Registered Successfully , Please visit the Company Office to Complete Registration Process");
    }


    public function adminRegister(EmployeeRegisterRequest $request): JsonResponse
    {
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

        $role = Role::query()->where('name', 'like', 'Admin')->get();
        $user->assignRole($role);
        return $this->getJsonResponse($user, "Admin Registered Successfully");
    }

    public function logout(): JsonResponse
    {
        Auth::logout();
        return $this->getJsonResponse([], "User Logged Out Successfully");
    }

    public function refresh(): JsonResponse
    {

        return $this->createNewToken(auth('api')->refresh());
    }

    public function userProfile(): JsonResponse
    {
        return response()->json(auth('api')->user());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return JsonResponse
     */
    protected function createNewToken(string $token): JsonResponse
    {
        $user = auth()->user();
        $user->load('roles');//$roles = $user->getRoleNames();
        //$vv=$user->can('Confirm Student Attendance');
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => $user//auth()->user(),
            //'roles'=> $roles
            //'test'=> $vv
        ]);
    }
}
