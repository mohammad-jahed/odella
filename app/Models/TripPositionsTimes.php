<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripPositionsTimes extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id', 'position_id', 'time'
    ];

    protected $table = 'trip_positions_times';

}
