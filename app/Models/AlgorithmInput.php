<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AlgorithmInput extends Model
{
    use HasFactory;

    protected $fillable = [
        'goTime',
        'returnTime'
    ];


    public function user(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
