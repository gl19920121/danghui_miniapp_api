<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Auth;
use App\Models\User;
use App\Models\Company;
use App\Models\JobPublisher;

class Job extends Model
{
    use HasFactory;

    public const STATUS_ALL = 'all';
    public const STATUS_ACTIVE = 'active';

    protected $perPage = 3;

    protected $connection = 'mysql_crm';

    protected $hidden = [
        'release_uid', 'execute_uid', 'channel'
    ];

    protected $casts = [
        'type' => 'json',
        'location' => 'json',
        'channel' => 'array',
        'is_collected' => 'boolean',
        'created_at' => 'datetime:Y-m-d h:m:s',
        'updated_at' => 'datetime:Y-m-d h:m:s',
    ];

    protected $appends = [
        'is_collected', 'experience_show', 'education_show', 'welfare_show', 'last_update_time', 'updated_date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function publisher()
    {
        return $this->belongsTo(JobPublisher::class, 'execute_uid');
    }

    public function collects()
    {
        $connection = 'mysql';
        return $this->setConnection($connection)->belongsToMany(User::class)->wherePivot('user_id', Auth::user()->id)->wherePivot('type', 'collect')->withTimestamps();
    }

    public function getIsCollectedAttribute(): bool
    {
        return $this->collects()->count() > 0 ? true : false;
    }

    public function getExperienceShowAttribute(): string
    {
        return config('lang.experience')[$this->experience];
    }

    public function getEducationShowAttribute(): string
    {
        return config('lang.education')[$this->education];
    }

    public function getWelfareShowAttribute(): string
    {
        return config('lang.welfare')[$this->welfare];
    }

    public function getLastUpdateTimeAttribute(): string
    {
        $updatedAt = Carbon::parse($this->updated_at);

        $days = (new Carbon)->diffInDays($updatedAt, true);
        if ($days > 7) {
            return $this->updated_date;
        } elseif ($days > 0) {
            return sprintf('%s天前', $days);
        }

        $hours = (new Carbon)->diffInHours($updatedAt, true);
        if ($hours > 0) {
            return sprintf('%s小时前', $hours);
        }

        $minutes = (new Carbon)->diffInMinutes($updatedAt, true);
        if ($minutes > 0) {
            return sprintf('%s分钟前', $minutes);
        }

        return '刚刚';
    }

    public function getUpdatedDateAttribute(): string
    {
        return Carbon::parse($this->updated_at)->format('Y-m-d');
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
                    return $query->where('status', 1);
                    break;

                default:
                    return $query->where('status', 1);
                    break;
            }
        }
    }

    public function scopeSearchByType($query, $type)
    {
        if (!empty($type)) {
            return $query->whereJsonContains('type', $type);
        }
    }

    public function scopeSearchByLocation($query, $location)
    {
        if (!empty($location)) {
            return $query->where('location->city', 'like', '%' . $location . '%');
        }
    }

    public function scopeSearchBySalary($query, $salaryMin, $salaryMax)
    {
        if (!empty($salaryMin)) {
            $query->where('salary_min', '>=', $salaryMin);
        }
        if (!empty($salaryMax)) {
            $query->where('salary_max', '<=', $salaryMax);
        }

        return $query;
    }

    public function scopeSearchByPubdate($query, $pubdate)
    {
        switch ($pubdate) {
            case 'today':
                return $query->whereDate('created_at', '=', Carbon::today()->toDateString());
                break;
            case 'lately':
                return $query->whereDate('created_at', '>', Carbon::today()->modify('-3 days')->toDateString());
                break;
            case 'week':
                return $query->whereDate('created_at', '>', Carbon::today()->modify('-7 days')->toDateString());
                break;
            case 'month':
                return $query->whereDate('created_at', '>', Carbon::today()->modify('-1 month')->toDateString());
                break;

            default:
                break;
        }
    }

    public function scopeSearchByExperience($query, $experience)
    {
        if (!empty($experience)) {
            if ($experience === 'noob') {
                return $query->whereIn('experience', ['school', 'fresh_graduates']);
            } else {
                return $query->where('experience', $experience);
            }
        }
    }

    public function scopeSearchByEducation($query, $education)
    {
        if (!empty($education)) {
            return $query->where('education', $education);
        }
    }

    public function scopeCollect($query)
    {
        // $connection = 'mysql';
        // return $this->setConnection($connection)->$query
        //     ->rightJoin('job_user', 'jobs.id', '=', 'job_user.job_id')
        //     ->where('job_user.user_id', Auth::user()->id)
        // ;
        $dbminiapp =  env('DB_DATABASE');
        // die(var_dump($dbminiapp));
        return $query
            ->rightJoin($dbminiapp.'.job_user', 'jobs.id', '=', 'job_user.job_id')
            ->where('job_user.user_id', Auth::user()->id)
        ;
    }
}
