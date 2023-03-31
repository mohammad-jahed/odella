<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'supervisor_id', 'bus_driver_id', 'time_id'
    ];

    public function times()
    {
        return $this->belongsToMany(Time::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'id', 'supervisor_id');
    }

    public function bus_driver()
    {
        return $this->belongsTo(BusDriver::class, 'id', 'bus_driver_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class,
            'trip_users', 'trip_id', 'user_id');
    }

    public function lines()
    {
        return $this->belongsToMany(TransportationLine::class,
            'trip_lines', 'trip_id', 'line_id');
    }
}
