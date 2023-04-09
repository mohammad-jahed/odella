<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Http\Requests\Supervisor\StoreSupervisorRequest;
use App\Http\Requests\Supervisor\UpdateSupervisorRequest;
use App\Models\Location;
use App\Models\User;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
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
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('View Supervisor')) {

            $supervisors = User::role('Supervisor')->get();

            return $this->getJsonResponse($supervisors, "Supervisors Fetched Successfully");

        } else {

            abort(Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSupervisorRequest $request)
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('Add Supervisor')) {

            try {

                DB::beginTransaction();
                /**
                 * @var User $user ;
                 */
                $credentials = $request->validated();

                $credentials['password'] = Hash::make($credentials['password']);

                if ($request->hasFile('image')) {

                    $path = $request->file('image')->store('images/supervisor');

                    $credentials['image'] = $path;
                }
                /**
                 * @var Location $location ;
                 */
                $location = Location::query()->create($credentials);

                $credentials['location_id'] = $location->id;

                $credentials['status'] = Status::NonStudents;

                $user = User::query()->create($credentials);

                $role = Role::query()->where('name', 'like', 'Supervisor')->first();

                $user->assignRole($role);

                DB::commit();

                return $this->getJsonResponse($user, "Supervisor Registered Successfully");

            } catch (Exception $exception) {

                DB::rollBack();

                return $this->getJsonResponse($exception->getMessage(), "Something Went Wrong!!");
            }

        } else {

            abort(Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $supervisor)
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('View Supervisor')) {

            return $this->getJsonResponse($supervisor, "Supervisor Fetched Successfully");

        } else {

            abort(Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Update the specified resource in storage.
     * @throws AuthorizationException
     */
    public function update(UpdateSupervisorRequest $request, User $supervisor): JsonResponse
    {
        /**
         * @var User $auth ;
         */
        $auth = auth()->user();

        Gate::forUser($auth)->authorize('updateProfile', $supervisor);

        try {

            DB::beginTransaction();

            $credentials = $request->validated();

            if (isset($credentials['newPassword'])) {

                $credentials['password'] = Hash::make($credentials['newPassword']);
            }
            if ($request->hasFile('image')) {

                $path = $request->file('image')->store('images/supervisor');

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
            $location = $supervisor->location;

            $location->update($data);

            $supervisor->update($credentials);

            DB::commit();

            return $this->getJsonResponse($supervisor, "Supervisor Updated Successfully");

        } catch (Exception $exception) {

            DB::rollBack();

            return $this->getJsonResponse($exception->getMessage(), "Something Went Wrong!!");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $supervisor)
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        if ($user->can('Delete Supervisor')) {

            $supervisor->delete();

            return $this->getJsonResponse(null, "Supervisor Deleted Successfully");

        } else {

            abort(Response::HTTP_FORBIDDEN);
        }
    }
}
