<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AlgorithmInput extends Model
{
    use HasFactory;

    protected $fillable = [
        'goTime',
        'returnTime'
    ];


    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'algorithm_input_users', 'algorithm_input_id', 'user_id');
    }

}
