<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SharedPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'transportation_line_id',
        'transfer_position_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];


}
