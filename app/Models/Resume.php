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
use App\Traits\SerializeDate;

class Resume extends Model
{
    use HasFactory;
    use SerializeDate;

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

    protected $appends = [
        'work_years_show', 'education_show', 'personal_advantage_show', 'avatar_url', 'rungs',
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

    public function getWorkYearsShowAttribute(): String
    {
        if ($this->work_years_flag === 0) {
            return sprintf('%s年经验', $this->work_years);
        } else {
            return config('lang.resume.work_years')[$this->work_years_flag];
        }
    }

    public function getEducationShowAttribute(): String
    {
        return config('lang.education')[$this->education];
    }

    public function getPersonalAdvantageShowAttribute(): String
    {
        return $this->personal_advantage ?? '请填写自我描述';
    }

    public function getAvatarUrlAttribute()
    {
        if (!empty($this->avatar)) {
            $url = Storage::disk('resume_avatar')->url($this->attributes['avatar']);
        } elseif ($this->sex === '女') {
            $url = 'images/avatar_default_yellow_man.png'; // female
        } else {
            $url = 'images/avatar_default_yellow_man.png';
        }

        return asset($url);
    }

    public function getRungsAttribute(): Int
    {
        $rungs = 0;

        $total = $this->only('avatar', 'name', 'sex', 'age', 'location', 'work_years_flag', 'education', 'major', 'phone_num', 'email', 'wechat', 'qq', 'cur_industry', 'cur_position', 'cur_company', 'cur_salary_flag', 'exp_industry', 'exp_position', 'exp_work_nature', 'exp_location', 'exp_salary_flag', 'jobhunter_status', 'social_home', 'personal_advantage', 'blacklist', 'attachment_path');
        $sum = count($total);
        $active = 0;
        foreach ($total as $key => $value) {
            $active += empty($value) ? 0 : 1;
        }

        $rungs = round($active / $sum, 2) * 100;

        return $rungs;
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
        return $query->where('upload_uid', Auth::user()->openid);
    }
}
