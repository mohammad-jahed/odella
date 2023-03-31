<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripLines extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id', 'line_id'
    ];
}
