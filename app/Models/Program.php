<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Program extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'day_id',
        'position_id',
        'start',
        'end'
    ];


    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function day(): BelongsTo {
        return $this->belongsTo(Day::class);
    }

    public function position(): BelongsTo {
        return $this->belongsTo(TransferPosition::class);
    }

}
