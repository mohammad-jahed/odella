<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property Time time;
 * @property User $supervisor;
 * @property User[] $users;
 * @property TransportationLine[] $lines;
 * @property TransferPosition[] $transferPositions;
 */
class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'supervisor_id',
        'bus_driver_id',
        'time_id',
        'status'
    ];

    protected $table = 'trips';

    public function time(): BelongsTo
    {
        return $this->belongsTo(Time::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id', 'id');
    }

    public function busDriver(): BelongsTo
    {
        return $this->belongsTo(BusDriver::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class,
            'trip_users', 'trip_id', 'user_id');
    }

    public function lines(): BelongsToMany
    {
        return $this->belongsToMany(TransportationLine::class,
            'trip_lines', 'trip_id', 'line_id');
    }

    public function transferPositions(): BelongsToMany
    {
        return $this->belongsToMany(TransferPosition::class,
            'trip_positions_times', 'trip_id', 'position_id');

    }
}
