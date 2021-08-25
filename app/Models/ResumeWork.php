<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Resume;

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
        'start_at' => 'date:Y-m',
        'end_at' => 'date:Y-m',
        'is_not_end' => 'boolean',
    ];

    protected $appends = ['job_type_show', 'duration'];

    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }

    public function getJobTypeShowAttribute(): String
    {
        return $this->job_type['rd'];
    }

    public function getStartAtShowAttribute(): String
    {
        return date('Y.m', strtotime($this->start_at));
    }

    public function getEndAtShowAttribute(): String
    {
        return date('Y.m', strtotime($this->start_at));
    }

    public function getDurationAttribute()
    {
        if ($this->is_not_end) {
            $duration = sprintf('%s-至今', $this->start_at_show);
        } else {
            $duration = sprintf('%s-%s', $this->start_at_show, $this->end_at_show);
        }

        return $duration;
    }

    // public function setIsNotEndAttribute($value)
    // {
    //     $this->attributes['is_not_end'] = $value === 'on' ? true : false;
    // }
}
