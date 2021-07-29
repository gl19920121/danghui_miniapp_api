<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;
use App\Models\User;
use App\Models\Job;
use App\Models\ResumeWork;
use App\Models\ResumePrj;
use App\Models\ResumeEdu;

class Resume extends Model
{
    use HasFactory;

    public const STATUS_ALL = 'all';
    public const STATUS_ACTIVE = 'active';

    protected $perPage = 10;

    protected $connection = 'mysql_crm';

    protected $fillable = [];

    protected $guarded = [];

    protected $hidden = [
        'source', 'source_remarks', 'upload_uid',
    ];

    protected $casts = [
        'age' => 'integer',
        'work_years_flag' => 'integer',
        'work_years' => 'integer',
        'cur_salary_count' => 'integer',
        'exp_salary_flag' => 'integer',
        'exp_salary_count' => 'integer',
        'jobhunter_status' => 'integer',
        'cur_salary' => 'float',
        'exp_salary_min' => 'float',
        'exp_salary_max' => 'float',
        'location' => 'json',
        'cur_industry' => 'json',
        'cur_position' => 'json',
        'exp_industry' => 'json',
        'exp_position' => 'json',
        'exp_location' => 'json',
        'source' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'upload_uid')->where('upload_uid', Auth::user()->id);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function resumeWorks()
    {
        return $this->hasMany(ResumeWork::class);
    }

    public function resumePrjs()
    {
        return $this->hasMany(ResumePrj::class);
    }

    public function resumeEdus()
    {
        return $this->hasMany(ResumeEdu::class);
    }

    public function scopeStatus($query, $status = self::STATUS_ACTIVE)
    {
        if (is_numeric($status)) {
            return $query->where('status', $status);
        } else {
            switch ($status) {
                case self::STATUS_ALL:
                    break;
                case self::STATUS_ACTIVE:
                    return $query->whereNotIn('status', []);
                    break;

                default:
                    break;
            }
        }
    }

    public function scopeMy($query)
    {
        return $query->where('upload_uid', Auth::user()->id);
    }
}
