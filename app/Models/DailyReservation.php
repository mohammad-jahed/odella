<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

/**
 * @property Trip $trip;
 * @property integer $guestRequestStatus;
 * @property mixed $fcm_token
 * @property integer trip_id
 * @property integer transfer_position_id
 * @property integer $id
 */
class DailyReservation extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'phoneNumber',
        'guestRequestStatus',
        'trip_id',
        'seatsNumber',
        'transfer_position_id',
        'fcm_token'
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(TransferPosition::class, 'transfer_position_id');
    }


}
