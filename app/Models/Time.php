<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Date;

/**
 * @property Date $start;
 */
class Time extends Model
{
    use HasFactory;

    protected $fillable = [
        'start', 'date'
    ];

    protected $table = 'times';

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }
}
