<?php

namespace App\Providers;
use App\Models\Area;
use App\Models\City;
use App\Models\Location;
use App\Models\Program;
use App\Models\Subscription;
use App\Models\TransferPosition;
use App\Models\TransportationLine;
use App\Models\User;
use App\Policies\AreaPolicy;
use App\Policies\CityPolicy;
use App\Policies\EmployeeAndSupervisorPolicy;
use App\Policies\LocationPolicy;
use App\Policies\ProgramPolicy;
use App\Policies\SubscriptionPolicy;
use App\Policies\TransferPositionPolicy;
use App\Policies\TransportationLinePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        City::class => CityPolicy::class,
        Area::class => AreaPolicy::class,
        Location::class => LocationPolicy::class,
        Subscription::class => SubscriptionPolicy::class,
        TransportationLine::class => TransportationLinePolicy::class,
        TransferPosition::class => TransferPositionPolicy::class,
        User::class => EmployeeAndSupervisorPolicy::class,
        Program::class => ProgramPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {

        //Locations
        Gate::define('getAllLocations', [LocationPolicy::class,'viewAny']);
        Gate::define('getLocation', [LocationPolicy::class,'view']);
        Gate::define('updateLocation', [LocationPolicy::class,'update']);
        Gate::define('deleteLocation', [LocationPolicy::class,'delete']);
        //Employees & Supervisor & Student
        Gate::define('updateProfile', [EmployeeAndSupervisorPolicy::class,'update']);

        //Program
        Gate::define('viewProgram', [ProgramPolicy::class,'view']);
        Gate::define('updateProgram', [ProgramPolicy::class,'update']);
        Gate::define('deleteProgram', [ProgramPolicy::class,'delete']);

    }
}
