<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Bus $bus;
 */

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


    public function bus(): BelongsTo {
        return $this->belongsTo(Bus::class);
    }


    public function driver(): BelongsTo {
        return $this->belongsTo(Driver::class);
    }
}
