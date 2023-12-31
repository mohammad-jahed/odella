<?php

namespace App\Http\Controllers;

use  App\Enums\ConfirmationCodeStatus;
use App\Enums\ConfirmationCodeTypes;
use App\Enums\Status;
use App\Http\Requests\Auth\EmailConfirmationRequest;
use App\Http\Requests\Auth\ForgetPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Resources\UserResource;
use App\Mail\ForgetPasswordMail;
use App\Mail\RegisterConfirmationMail;
use App\Models\ConfirmationCode;
use App\Models\Location;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\Employees\PendingUserRegisterNotification;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'register1', 'emailConfirmation', 'forgetPassword', 'resetPassword']]);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        //$data = $request->validated();
        $data = ['email' => $request->email];
        $data += ['password' => $request->password];

        if (!$token = auth('api')->attempt($data)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        /**
         * @var User $user ;
         */
        $user = auth()->user();
        if ($user->status == Status::UnActive) {

            Auth::logout();
            return $this->getJsonResponse(null, "Un authorized, Please visit the Company Office to Complete Registration Process", 0);
        }

        if (isset($request['fcm_token'])) {

            $user->fcm_token = $request['fcm_token'];
            $user->save();
        }


        return $this->createNewToken($token);
    }


    public function register(RegisterRequest $request): JsonResponse
    {

        try {
            $credentials = $request->validated();

            if ($request->hasFile('image')) {

                $path = $request->file('image')->store('images/users');

                $credentials['image'] = $path;
            }

            DB::beginTransaction();
            /**
             * @var User $user ;
             * @var Subscription $subscription ;
             */
            $credentials['password'] = Hash::make($credentials['password']);

            /**
             * @var Location $location ;
             */
            $location = Location::query()->create($credentials);

            $credentials['location_id'] = $location->id;

            $user = User::query()->create($credentials);

            $role = Role::query()->where('name', 'like', 'Student')->first();

            $user->assignRole($role);

            DB::commit();

            /**
             * @var User $employees ;
             */
            $employees = User::role('Employee')->get();

            Notification::send($employees, new PendingUserRegisterNotification($user));

            return $this->getJsonResponse($user, "User Registered Successfully , Please visit the Company Office to Complete Registration Process");

        } catch (Exception $exception) {

            DB::rollBack();

            return $this->getJsonResponse($exception->getMessage(), "Something Went Wrong!!", 0);
        }

    }

//ToDo
    public function register1(RegisterRequest $request): JsonResponse
    {

        try {
            $credentials = $request->validated();

            if ($request->hasFile('image')) {

                $path = $request->file('image')->store('images/users');

                $credentials['image'] = $path;
            }

            DB::beginTransaction();
            /**
             * @var User $user ;
             * @var Subscription $subscription ;
             */
            $credentials['password'] = Hash::make($credentials['password']);

            /**
             * @var Location $location ;
             */
            $location = Location::query()->create($credentials);

            $credentials['location_id'] = $location->id;

            $credentials['status'] = Status::NOT_CONFIRMED;

            $user = User::query()->create($credentials);

            $role = Role::query()->where('name', 'like', 'Student')->first();

            $user->assignRole($role);

            $code = rand(10000, 99999);

            $data = [
                'user_id' => $user->id,
                'confirm_code' => $code,
                'type' => ConfirmationCodeTypes::Confirmation
            ];

            $confirmCode = ConfirmationCode::query()->create($data);

            if (!$confirmCode->save()) {

                DB::rollBack();

                return $this->getJsonResponse(null, "Code Not Save!", 0);
            }

            Mail::to($user->email)->send((new RegisterConfirmationMail($user, $code))->afterCommit());

            DB::commit();

            return $this->getJsonResponse(null, "We Send A Confirmation Code to your Email");


        } catch (Exception $exception) {

            DB::rollBack();

            return $this->getJsonResponse($exception->getMessage(), "Something Went Wrong!!", 0);
        }

    }

    public function emailConfirmation(EmailConfirmationRequest $request): JsonResponse
    {

        try {
            /**
             * @var User $user ;
             */

            $user = User::query()->where('email', $request->email)->first();

            if (!$user) {

                return $this->getJsonResponse(null, "User Not Found", 0);
            }

            $code = ConfirmationCode::query()->where('user_id', $user->id)
                ->where('confirm_code', $request->code)
                ->where('is_confirmed', ConfirmationCodeStatus::NotConfirmed)
                ->where('type', ConfirmationCodeTypes::Confirmation)
                ->first();

            if (!$code) {

                return $this->getJsonResponse(null, "Wrong Code!", 0);
            }

            DB::beginTransaction();

            $user->status = Status::UnActive;

            $user->save();

            $code->is_confirmed = ConfirmationCodeStatus::Confirmed;

            $code->save();

            /**
             * @var User $employees ;
             */
            $employees = User::role('Employee')->get();

            Notification::send($employees, new PendingUserRegisterNotification($user));

            DB::commit();

            return $this->getJsonResponse(null, "Email Confirmed Successfully,
            Please visit the Company Office to Complete Registration Process");

        } catch (Exception $exception) {

            DB::rollBack();

            return $this->getJsonResponse($exception->getMessage(), "Some Thing Went Wrong!!", 0);

        }

    }

    public function forgetPassword(ForgetPasswordRequest $request): JsonResponse
    {

        try {

            /**
             * @var User $user ;
             */

            $user = User::query()->where('email', $request->email)->first();

            if (!$user) {
                return $this->getJsonResponse(null, "User Not Found");
            }

            $checkCode = ConfirmationCode::query()->where('user_id', $user->id)
                ->where('is_confirmed', ConfirmationCodeStatus::NotConfirmed)
                ->where('type',ConfirmationCodeTypes::ForgetPassword)
                ->first();

            if ($checkCode) {
                return $this->getJsonResponse(null, "Code Already Sent", 0);
            }

            DB::beginTransaction();

            $code = rand(10000, 99999);

            $data = [
                'user_id' => $user->id,
                'confirm_code' => $code,
                'type' => ConfirmationCodeTypes::ForgetPassword
            ];

            $confirmCode = ConfirmationCode::query()->create($data);

            if (!$confirmCode->save()) {
                DB::rollBack();
                return $this->getJsonResponse(null, "Code Not Save!", 0);
            }

            Mail::to($user->email)->send((new ForgetPasswordMail($user, $code))->afterCommit());

            DB::commit();

            return $this->getJsonResponse(null, "We Send A Reset Password Code to your Email");

        } catch (Exception $exception) {

            DB::rollBack();

            return $this->getJsonResponse($exception->getMessage(), "Something Went Wrong!!", 0);
        }


    }


    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {

        try {
            /**
             * @var User $user ;
             */

            $user = User::query()->where('email', $request->email)->first();

            if (!$user) {

                return $this->getJsonResponse(null, "User Not Found", 0);
            }

            $code = ConfirmationCode::query()->where('user_id', $user->id)
                ->where('confirm_code', $request->code)
                ->where('is_confirmed', ConfirmationCodeStatus::NotConfirmed)
                ->where('type', ConfirmationCodeTypes::ForgetPassword)
                ->first();

            if (!$code) {

                return $this->getJsonResponse(null, "Wrong Code!", 0);
            }

            DB::beginTransaction();

            $newPassword = Hash::make($request->newPassword);

            $user->password = $newPassword;

            $user->save();

            $code->is_confirmed = ConfirmationCodeStatus::Confirmed;

            $code->save();

            DB::commit();

            return $this->getJsonResponse(null, "Password Reset Successfully");

        } catch (Exception $exception) {

            DB::rollBack();

            return $this->getJsonResponse($exception->getMessage(), "Some Thing Went Wrong!!", 0);

        }

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
        /**
         * @var User $user ;
         */
        $user = auth()->user();
        $user->load('roles');//$roles = $user->getRoleNames();
        //$vv=$user->can('Confirm Student Attendance');
        return response()->json([
            'message' => 'User Login Successfully',
            'status' => 1,
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,
                'user' => $user
            ],
        ]);
    }


    public function profile(): JsonResponse
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        $user->load('roles','university','line','position','subscription','location');

        $user = new UserResource($user);

        return $this->getJsonResponse($user, "Profile");

    }

}
