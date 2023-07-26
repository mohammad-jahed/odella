<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Date;

/**
 * @property Date $start;
 * @property Date $date;
 * @property int $id;
 */
class Time extends Model
{
    use HasFactory;

    protected $fillable = [
        'start',
        'date',
        'day'
    ];




    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'date' => 'datetime'
    ];


    protected $table = 'times';

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }
}
