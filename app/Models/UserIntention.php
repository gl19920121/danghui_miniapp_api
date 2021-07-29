<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserIntention extends Model
{
    use HasFactory;

    protected $casts = [
        'position' => 'json',
        'industry' => 'json',
        'salary' => 'json',
        'created_at' => 'datetime:Y-m-d h:m:s',
        'updated_at' => 'datetime:Y-m-d h:m:s',
    ];

    protected $fillable = [
        'type', 'city', 'position', 'industry', 'salary', 'user_id',
    ];
}
