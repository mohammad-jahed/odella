<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id;
 * @property mixed $positions
 * @method static where(string $string, mixed $line_id)
 */
class TransportationLine extends Model
{
    use HasFactory;

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
        'from',
        'to',
        'name'
    ];

    public function getNameAttribute()
    {
        return $this->{'name_' . app()->getLocale()};
    }

    public function getFromAttribute()
    {
        return $this->positions()->first();
    }

    public function getToAttribute()
    {
        return $this->positions()->orderBy('id', 'desc')->first();
    }


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

    public function trips(): BelongsToMany
    {
        return $this->belongsToMany(Trip::class,
            'trip_lines', 'line_id', 'trip_id');
    }


}
