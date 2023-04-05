<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trip_id',
        'studentAttendance'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];


}
