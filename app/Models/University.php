<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static create(mixed $data)
 */
class University extends Model
{
    use HasFactory;
    protected $fillable = [
      'name_ar',
      'name_en',
      'shortcut'
    ];

    public function users(): HasMany {
        return $this->hasMany(User::class);
    }
}
