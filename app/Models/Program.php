<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Date;

/**
 * @property User $user
 * @property Day $day;
 * @property Date $start;
 * @property Date $end;
 */
class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'day_id',
        'transfer_position_id',
        'start',
        'end'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function day(): BelongsTo
    {
        return $this->belongsTo(Day::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(TransferPosition::class, 'transfer_position_id');
    }

}
