<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Area;
use App\Models\City;
use App\Models\Location;
use App\Policies\AreaPolicy;
use App\Policies\CityPolicy;
use App\Policies\LocationPolicy;
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
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        City::class => CityPolicy::class,
        Area::class => AreaPolicy::class,
        Location::class => LocationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Cities
        Gate::define('createCity', [CityPolicy::class,'create']);
        Gate::define('updateCity', [CityPolicy::class,'update']);
        Gate::define('deleteCity', [CityPolicy::class,'delete']);
        // Areas
        Gate::define('createArea', [AreaPolicy::class,'create']);
        Gate::define('updateArea', [AreaPolicy::class,'update']);
        Gate::define('deleteArea', [AreaPolicy::class,'delete']);
        //Locations
        Gate::define('getAllLocations', [LocationPolicy::class,'viewAny']);
        Gate::define('getLocation', [LocationPolicy::class,'view']);
        Gate::define('updateLocation', [LocationPolicy::class,'update']);
        Gate::define('deleteLocation', [LocationPolicy::class,'delete']);



    }
}
