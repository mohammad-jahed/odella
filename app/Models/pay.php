<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pay extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount', 'date'
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function payments()
    {
        return $this->belongsToMany(payment::class);
    }
}
