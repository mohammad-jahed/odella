<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\City;
use App\Policies\CityPolicy;
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
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Cities
        Gate::define('createCity', [CityPolicy::class,'create']);
        Gate::define('getAllCities', [CityPolicy::class,'viewAny']);
        Gate::define('getCity', [CityPolicy::class,'view']);
        Gate::define('updateCity', [CityPolicy::class,'update']);
        Gate::define('deleteCity', [CityPolicy::class,'delete']);


    }
}
