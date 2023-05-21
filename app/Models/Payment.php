<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Pay $pay
 * @property mixed $subscription_id
 */
class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pay_id'
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pay(): BelongsTo
    {
        return $this->belongsTo(Pay::class, 'pay_id');
    }

}
