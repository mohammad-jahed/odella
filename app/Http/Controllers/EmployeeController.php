<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Http\Requests\Employee\ConfirmRegistrationRequest;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Models\Location;
use App\Models\Pay;
use App\Models\Program;
use App\Models\Subscription;
use App\Models\Trip;
use App\Models\TripPositionsTimes;
use App\Models\User;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
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
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('View Employee')) {

            $employees = User::role('Employee')->get();

            if ($employees->isEmpty()) {

                return $this->getJsonResponse(null, "There Are No Employees Found!");
            }

            return $this->getJsonResponse($employees, "Employees Fetched Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        /**
         * @var User $auth ;
         */
        $auth = auth()->user();

        if ($auth->can('Add Employee')) {

            try {

                DB::beginTransaction();

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

                $credentials['status'] = Status::NonStudent;
                /**
                 * @var User $user ;
                 */
                $user = User::query()->create($credentials);

                $role = Role::query()->where('name', 'like', 'Employee')->first();

                $user->assignRole($role);

                DB::commit();

                return $this->getJsonResponse($user, "Employee Registered Successfully");

            } catch (Exception $exception) {

                DB::rollBack();

                return $this->getJsonResponse($exception->getMessage(), "Something Went Wrong!!");
            }

        } else {

            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(User $employee): JsonResponse
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('View Employee')) {

            return $this->getJsonResponse($employee, "Employee Fetched Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
        }
    }

    /**
     * Update the specified resource in storage.
     * @throws AuthorizationException
     */
    public function update(UpdateEmployeeRequest $request, User $employee): JsonResponse
    {

        /**
         * @var User $auth ;
         */

        $auth = auth()->user();

        Gate::forUser($auth)->authorize('updateProfile', $employee);

        try {

            DB::beginTransaction();

            $credentials = $request->validated();

            if (isset($credentials['newPassword'])) {
                $credentials['password'] = Hash::make($credentials['newPassword']);
            }
            if ($request->hasFile('image')) {

                $path = $request->file('image')->store('images/employee');

                $credentials['image'] = $path;
            }
            /**
             * @var Location $location ;
             */
            $data = [];

            if (isset($credentials['city_id'])) {
                $data += ['city_id' => $credentials['city_id']];
            }
            if (isset($credentials['area_id'])) {
                $data += ['area_id' => $credentials['area_id']];
            }
            if (isset($credentials['street'])) {
                $data += ['street' => $credentials['street']];
            }
            $location = $employee->location;

            $location->update($data);

            $employee->update($credentials);

            DB::commit();

            return $this->getJsonResponse($employee, "Employee Updated Successfully");

        } catch (Exception $exception) {

            DB::rollBack();

            return $this->getJsonResponse($exception->getMessage(), "Something Went Wrong!!");

        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $employee)
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('Delete Employee')) {

            $employee->delete();

            return $this->getJsonResponse(null, "Employee Deleted Successfully");

        } else {

            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
        }
    }

    /**
     */
    public function confirmRegistration(User $user, ConfirmRegistrationRequest $request)
    {
        /**
         * @var User $auth ;
         */
//        $auth = auth()->user();
//
//        if ($auth->can('Confirm registration')) {

            try {
                /**
                 * @var User $auth ;
                 * @var Subscription $subscription ;
                 */

                $credentials = $request->validated();
                $user->expiredSubscriptionDate = $credentials['expiredSubscriptionDate'];
                $user->save();
                $user->trips()->attach($credentials['trip_ids']);
                /**
                 * @var Trip[] $trips ;
                 * @var Trip[] $goTrips ;
                 * @var Trip[] $returnTrips ;
                 * @var TripPositionsTimes $goTime ;
                 * @var TripPositionsTimes $returnTime ;
                 */
                $trips = $user->trips;
                $goTrips = [];
                $returnTrips = [];
                for ($i = 0; $i < sizeof($trips); $i++) {
                    $trips[$i]->status == 1 ? $goTrips += [$trips[$i]] : $returnTrips += [$trips[$i]];
                }
                foreach (array_intersect_key($credentials['day_ids'], $credentials['position_ids']) as $key => $value) {
                    foreach (array_intersect_key($goTrips, $returnTrips) as $k => $v) {
                        $firstPosition = $goTrips[$k]->transferPositions()->first();
                        $goTime = TripPositionsTimes::query()->where('position_id', $firstPosition->id)->first();
                        $data = [
                            'day_id' => $credentials['day_ids'][$key],
                            'transfer_position_id' => $credentials['position_ids'][$key],
                            'start' => $goTime->time,
                            'end' => $returnTrips[$k]->time->start,
                            'user_id' => $user->id
                        ];
                        Program::query()->create($data);
                    }
                }
                $pay = Pay::query()->create($credentials);

                $user->payments()->attach($pay);

                $user->update(['status' => Status::Active]);

                $user->load('payments');

                DB::commit();

                return $this->getJsonResponse($user, "Your Register Is Confirmed Successfully");

            } catch (Exception $exception) {

                DB::rollBack();

                return $this->getJsonResponse($exception->getMessage(), "Something Went Wrong!!");
            }

//        } else {
//
//            abort(Response::HTTP_UNAUTHORIZED
//                , "Unauthorized , You Dont Have Permission To Access This Action");
//        }
    }

}
