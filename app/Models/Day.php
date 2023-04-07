<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string name_en;
 */
class Day extends Model
{
    use HasFactory;
    public function programs(): HasMany {
        return $this->hasMany(Program::class);
    }
}
