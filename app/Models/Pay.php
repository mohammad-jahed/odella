<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property float amount;
 * @property mixed $id
 */
class Pay extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount', 'date'
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function pays(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'payments', 'pay_id', 'user_id')
            ->withPivot(['subscription_id', 'isFinished']);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

}
