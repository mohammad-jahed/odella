<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AlgorithmInput extends Model
{
    use HasFactory;

    protected $fillable = [
        'day_id',
        'goTime',
        'returnTime'
    ];


    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'algorithm_input_users', 'algorithm_input_id', 'user_id');
    }

    public function day(): BelongsTo
    {
        return $this->belongsTo(Day::class);

    }

}
