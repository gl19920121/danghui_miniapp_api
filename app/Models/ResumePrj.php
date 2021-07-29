<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResumePrj extends Model
{
    use HasFactory;

    protected $connection = 'mysql_crm';

    protected $guarded = ['id'];

    protected $casts = [
        'start_at' => 'date',
        'end_at' => 'date',
        'is_not_end' => 'boolean',
    ];
}
