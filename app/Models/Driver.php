<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'firstname',
        'lastname',
        'number'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function buses(): BelongsToMany
    {
        return $this->belongsToMany(
            Bus::class,
            'bus_drivers',
            'driver_id',
            'bus_id'
        );
    }
}
