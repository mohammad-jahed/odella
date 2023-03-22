<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TransferPosition extends Model
{
    protected $fillable = [
        "name_ar",
        "name_en"
    ];


    public function lines(): BelongsToMany
    {
        return $this->belongsToMany(
            TransportationLine::class,
            'shared_positions',
            'transfer-position_id',
            'transportation_line_id'
        );
    }
}
