<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_language extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'language_id',
        'status',
    ];
}
