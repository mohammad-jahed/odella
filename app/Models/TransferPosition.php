<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id;
 */
class TransferPosition extends Model
{
    protected $fillable = [
        "name_ar",
        "name_en"
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        "name_ar",
        "name_en"
    ];

    protected $appends = [
        'name'
    ];

    public function getNameAttribute()
    {
        return $this->{'name_' . app()->getLocale()};
    }

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

    public function trips(): BelongsToMany
    {
        return $this->belongsToMany(Trip::class,
            'trip_positions_times', 'position_id', 'trip_id');
    }
}
