<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property integer $id;
 */
class Bus extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'capacity',
        'details',
        'image'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function drivers(): BelongsToMany
    {
        return $this->belongsToMany(
            Driver::class,
            'bus_drivers',
            'bus_id',
            'driver_id');
    }
}
