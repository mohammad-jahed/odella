<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Time $time;
 */
class TripPositionsTimes extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'position_id',
        'time'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $table = 'trip_positions_times';

}
