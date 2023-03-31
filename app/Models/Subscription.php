<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $daysNumber;
 */
class Subscription extends Model
{
    use HasFactory;
    protected $fillable = [
        'name_ar',
        'name_en',
        'daysNumber',
        'price'
    ];


    public function users(): HasMany {
        return $this->hasMany(User::class);
    }
}
