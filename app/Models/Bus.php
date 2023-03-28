<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Bus extends Model
{
    use HasFactory;

    protected $fillable = [
        'key', 'capacity', 'details', 'image'
    ];

    public function Drivers(): BelongsToMany
    {
        return $this->belongsToMany(Driver::class);
    }
}
