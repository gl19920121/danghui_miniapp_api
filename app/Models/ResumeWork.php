<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResumeWork extends Model
{
    use HasFactory;

    protected $connection = 'mysql_crm';

    protected $guarded = ['id'];

    protected $casts = [
        'company_scale' => 'integer',
        'company_industry' => 'json',
        'salary' => 'double',
        'salary_count' => 'integer',
        'job_type' => 'json',
        'subordinates' => 'integer',
        'start_at' => 'date',
        'end_at' => 'date',
        'is_not_end' => 'boolean',
    ];

    // public function setIsNotEndAttribute($value)
    // {
    //     $this->attributes['is_not_end'] = $value === 'on' ? true : false;
    // }
}
