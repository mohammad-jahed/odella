<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransferPosition extends Model
{
    protected $fillable = [
        "name_ar",
        "name_en"
    ];


    public function lines(): BelongsToMany
    {
        return $this->belongsToMany(
            TransportationLine::class,
            'shared_positions',
            'transfer_position_id',
            'transportation_line_id'
        );
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    public function trips()
    {
        return $this->belongsToMany(Trip::class,
            'trip_positions_times', 'position_id', 'trip_id');
    }
}
