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
use App\Models\User;
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
        //
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

            DB::transaction(function () use ($request) {

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
                 * @var User $user ;
                 */
                $user = User::query()->create($credentials);

                $role = Role::query()->where('name', 'like', 'Employee')->first();

                $user->assignRole($role);

                return $this->getJsonResponse($user, "Employee Registered Successfully");

            });
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
    public function update(UpdateEmployeeRequest $request, User $employee)
    {

        /**
         * @var User $auth ;
         */

        $auth = auth()->user();

        Gate::forUser($auth)->authorize('updateProfile', $employee);

        DB::transaction(function () use ($request, $employee) {

            $credentials = $request->validated();

            if (isset($credentials['password'])) {

                $credentials['password'] = Hash::make($credentials['password']);
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

            return $this->getJsonResponse($employee, "Employee Updated Successfully");

        });

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
    public function confirmRegistration(User $user, ConfirmRegistrationRequest $request)
    {

        $auth = auth()->user();

        if ($auth->can('Confirm registration')) {

            DB::transaction(function () use ($user, $request) {

                /**
                 * @var User $auth ;
                 * @var Subscription $subscription ;
                 */

                $credentials = $request->validated();
                $user->trips()->attach($credentials['trip_ids']);
                /**
                 * @var Trip[] $trips ;
                 * @var Trip[] $goTrips ;
                 * @var Trip[] $returnTrips ;
                 */
                $trips = $user->trips;
                $goTrips = [];
                $returnTrips = [];
                for ($i = 0; $i < sizeof($trips); $i++) {
                    $i % 2 == 0 ? $goTrips += [$trips[$i]] : $returnTrips += [$trips[$i]];
                }

                for ($i = 0; $i < sizeof($credentials['day_ids']); $i++) {
                    for ($j = 0; $j < sizeof($credentials['position_ids']); $j++) {
                        for ($k = 0; $k < sizeof($goTrips); $k++) {
                            for ($l = 0; $l < sizeof($returnTrips); $l++) {
                                if ($i == $j && $l == $k) {
                                    $data = [
                                        'day_id' => $credentials['day_ids'][$i],
                                        'transfer_position_id' => $credentials['position_ids'][$j],
                                        'start' => $goTrips[$k]->time->start,
                                        'end' => $returnTrips[$l]->time->start,
                                        'user_id' => $user->id
                                    ];
                                    Program::query()->create($data);
                                }
                            }
                        }
                    }
                }
                $pay = Pay::query()->create($credentials);

                $user->payments()->attach($pay);

                $user->update(['status' => Status::Active]);

                $user->load('payments');

                return $this->getJsonResponse($user, "Your Register Is Confirmed Successfully");

            });

        } else {

            abort(Response::HTTP_FORBIDDEN);
        }
    }

}
