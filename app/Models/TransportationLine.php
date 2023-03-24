<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property integer $id;
 * @property mixed $positions
 */

class TransportationLine extends Model
{
    use HasFactory;

    protected $fillable = [
        "name_ar",
        "name_en"
    ];


    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(
            TransferPosition::class,
            'shared_positions',
            'transportation_line_id',
            'transfer_position_id'
        );
    }


}
