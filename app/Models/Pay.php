<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Pay extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount', 'date'
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function payments(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'payments', 'pay_id', 'user_id');
    }
}
