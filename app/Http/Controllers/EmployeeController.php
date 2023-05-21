<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Http\Requests\Employee\ConfirmRegistrationRequest;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Http\Requests\Employee\UpdateStudentSubscriptionRequest;
use App\Http\Resources\UserResource;
use App\Models\Location;
use App\Models\Pay;
use App\Models\Program;
use App\Models\Subscription;
use App\Models\TransferPosition;
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

                $credentials = $request->validated();

                if ($request->hasFile('image')) {

                    $path = $request->file('image')->store('images/employee');

                    $credentials['image'] = $path;
                }

                DB::beginTransaction();

                $credentials['password'] = Hash::make($credentials['password']);

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

            $credentials = $request->validated();

            if ($request->hasFile('image')) {

                $path = $request->file('image')->store('images/employee');

                $credentials['image'] = $path;
            }

            DB::beginTransaction();

            if (isset($credentials['newPassword'])) {

                $credentials['password'] = Hash::make($credentials['newPassword']);
            }

            $locationData = array_intersect_key($credentials, array_flip(['city_id', 'area_id', 'street']));

            $employee->location->update($locationData);

            $employee->update($credentials);

            $employee = new UserResource($employee);

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
     * Confirms the registration of a new student.
     */
    public function confirmRegistration(User $user, ConfirmRegistrationRequest $request)
    {
        /**
         * @var User $auth ;
         */
        $auth = auth()->user();

        if ($auth->can('Confirm registration')) {

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
                 * @var TripPositionsTimes[] $goTimes ;
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

                        $positions = $goTrips[$k]->transferPositions()->get();
                        /**
                         * @var TransferPosition $position ;
                         */
                        foreach ($positions as $position) {
                            $tripPositionTime = TripPositionsTimes::query()->where('position_id', $position->id)->first();
                            $goTimes[] = $tripPositionTime;
                        }
                        $data = [
                            'day_id' => $credentials['day_ids'][$key],
                            'transfer_position_id' => $credentials['position_ids'][$key],
                            'start' => $goTimes[$key]->time,
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

        } else {

            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Dont Have Permission To Access This Action");
        }
    }

    /**
     * Update Student Subscription Information
     */

    public function updateStudentSubscription(UpdateStudentSubscriptionRequest $request, User $student)
    {
        /**
         * @var User $auth ;
         */
        $auth = auth()->user();
        if ($auth->hasRole('Employee')) {
            $data = $request->validated();
            if(isset($data['subscription_id'])){
                $student->subscription_id = $data['subscription_id'];
            }
            if(isset($data['expiredSubscriptionDate'])){
                $student->expiredSubscriptionDate = $data['expiredSubscriptionDate'];
            }
            $student->save();
            $student->load(['subscription', 'line', 'position', 'university', 'location']);
            $student = new UserResource($student);
            return $this->getJsonResponse($student, "Student Updated Successfully");
        } else {
            abort(Response::HTTP_UNAUTHORIZED
                , "Unauthorized , You Don't Have Permission To Access This Action");
        }
    }

}
