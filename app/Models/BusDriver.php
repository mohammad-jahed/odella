<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusDriver extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_id',
        'driver_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
