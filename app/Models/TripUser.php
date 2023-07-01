<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed pivot
 */
class TripUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trip_id',
        'studentAttendance'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];


    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
