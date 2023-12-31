<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Closure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Date;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @method static create(mixed $credentials)
 * @method static role(string $string)
 * @method whereHas(string $string, Closure $param)
 * @property integer $id;
 * @property integer $status;
 * @property mixed $location;
 * @property Subscription $subscription;
 * @property mixed $programs;
 * @property Trip[] $trips;
 * @property mixed $fcm_token
 * @property mixed $email
 * @property mixed|string $password
 * @property mixed $firstName
 * @property mixed $lastName
 * @property Date $expiredSubscriptionDate
 * @property int $subscription_id
 * @property mixed $payments
 * @property mixed $evaluations
 */
class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'location_id',
        'subscription_id',
        'transportation_line_id',
        'transfer_position_id',
        'university_id',
        'firstName',
        'lastName',
        'email',
        'password',
        'phoneNumber',
        'status',
        'guestRequestStatus',
        'image',
        'fcm_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }


    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function line(): BelongsTo
    {
        return $this->belongsTo(TransportationLine::class, 'transportation_line_id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(TransferPosition::class, 'transfer_position_id');
    }

    public function pays(): BelongsToMany
    {
        return
            $this->belongsToMany(Pay::class, 'payments', 'user_id', 'pay_id')
                ->withPivot(['subscription_id', 'isFinished']);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function trips(): BelongsToMany
    {
        return $this->belongsToMany(Trip::class,
            'trip_users', 'user_id', 'trip_id');
    }

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    public function lostAndFounds(): HasMany
    {
        return $this->hasMany(Lost_Found::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    public function my_notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }


    public function algorithm_inputs(): BelongsTo
    {
        return $this->belongsTo(AlgorithmInput::class, 'id');
    }

    public function tripUsers(): HasMany
    {
        return $this->hasMany(TripUser::class);
    }
}
