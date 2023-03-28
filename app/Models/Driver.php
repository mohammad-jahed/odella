<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'firstname', 'lastname', 'number'
    ];

    public function Buses(): BelongsToMany
    {
        return $this->belongsToMany(Bus::class);
    }
}
