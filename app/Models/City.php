<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property mixed $areas
 */
class City extends Model
{
    use HasFactory;

    protected $fillable = [
        "name_ar",
        "name_en"
    ];

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function areas(): HasMany
    {
        return $this->hasMany(Area::class);
    }


}
