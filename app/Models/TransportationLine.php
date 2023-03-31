<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id;
 * @property mixed $positions
 */
class TransportationLine extends Model
{
    use HasFactory;

    protected $fillable = [
        "name_ar",
        "name_en"
    ];


    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(
            TransferPosition::class,
            'shared_positions',
            'transportation_line_id',
            'transfer_position_id'
        );
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function trips()
    {
        return $this->belongsToMany(Trip::class,
            'trip_lines', 'line_id', 'trip_id');
    }


}
