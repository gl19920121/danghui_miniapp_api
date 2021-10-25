<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResumeEdu extends Model
{
    use HasFactory;

    protected $connection = 'mysql_crm';

    protected $guarded = ['id'];

    protected $casts = [
        'start_at' => 'date:Y.m',
        'end_at' => 'date:Y.m',
        'is_not_end' => 'boolean',
    ];

    protected $appends = ['school_level_show', 'duration'];

    public function getSchoolLevelShowAttribute(): String
    {
        return config('lang.education')[$this->school_level];
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
}
