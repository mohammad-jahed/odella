<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Area extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'name_ar',
        'name_en'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'name_ar',
        'name_en'
    ];

    protected $appends = [
        'name'
    ];

    public function getNameAttribute()
    {
        return $this->{'name_' . app()->getLocale()};
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }


}
