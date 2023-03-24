<?php

namespace App\Providers;
use App\Models\Area;
use App\Models\City;
use App\Models\Location;
use App\Models\Subscription;
use App\Models\TransferPosition;
use App\Models\TransportationLine;
use App\Policies\AreaPolicy;
use App\Policies\CityPolicy;
use App\Policies\LocationPolicy;
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
        //Subscriptions
        Gate::define('getAllSubscriptions', [SubscriptionPolicy::class,'viewAny']);
        Gate::define('getSubscription', [SubscriptionPolicy::class,'view']);
        Gate::define('createSubscription', [SubscriptionPolicy::class,'create']);
        Gate::define('updateSubscription', [SubscriptionPolicy::class,'update']);
        Gate::define('deleteSubscription', [SubscriptionPolicy::class,'delete']);
        //Lines
        Gate::define('createLine', [TransportationLinePolicy::class,'create']);
        Gate::define('updateLine', [TransportationLinePolicy::class,'update']);
        Gate::define('deleteLine', [TransportationLinePolicy::class,'delete']);
        //Positions
        Gate::define('createPosition', [TransferPositionPolicy::class,'create']);
        Gate::define('updatePosition', [TransferPositionPolicy::class,'update']);
        Gate::define('deletePosition', [TransferPositionPolicy::class,'delete']);
    }
}
