<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfirmationCode extends Model
{
    use HasFactory;

    protected $fillable = [
      'user_id',
      'confirm_code',
      'is_confirmed',
      'type'
    ];
}
