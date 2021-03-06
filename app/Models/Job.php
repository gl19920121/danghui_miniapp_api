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

    protected $perPage = 10;

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
        'is_collected', 'is_delivered', 'location_show', 'experience_show', 'education_show', 'welfare_show', 'nature_show', 'last_update_time', 'updated_date',
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

    public function delivers()
    {
        $connection = 'mysql';
        return $this->setConnection($connection)->belongsToMany(User::class)->wherePivot('user_id', Auth::user()->id)->wherePivot('type', 'deliver')->withTimestamps();
    }

    public function getLocationShowAttribute(): string
    {
        $location = [];
        if (!empty($this->location['province'])) {
            $location[] = $this->location['province'];
        }
        if (!empty($this->location['city'])) {
            $location[] = $this->location['city'];
        }
        if (!empty($this->location['district'])) {
            $location[] = $this->location['district'];
        }

        $show = implode('.', $location);
        return $show;
    }

    public function getSalaryCountAttribute(): int
    {
        return !empty($this->attributes['salary_count']) ? $this->attributes['salary_count'] : 12;
    }

    public function getIsCollectedAttribute(): bool
    {
        return $this->collects()->count() > 0 ? true : false;
    }

    public function getIsDeliveredAttribute(): bool
    {
        return $this->delivers()->count() > 0 ? true : false;
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

    public function getNatureShowAttribute(): string
    {
        return config('lang.job.nature')[$this->nature];
    }

    public function getLastUpdateTimeAttribute(): string
    {
        $updatedAt = Carbon::parse($this->updated_at);

        $days = (new Carbon)->diffInDays($updatedAt, true);
        if ($days > 7) {
            return $this->updated_date;
        } elseif ($days > 0) {
            return sprintf('%s??????', $days);
        }

        $hours = (new Carbon)->diffInHours($updatedAt, true);
        if ($hours > 0) {
            return sprintf('%s?????????', $hours);
        }

        $minutes = (new Carbon)->diffInMinutes($updatedAt, true);
        if ($minutes > 0) {
            return sprintf('%s?????????', $minutes);
        }

        return '??????';
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
            return $query->where('type->st', 'like', '%'.$type.'%');
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
        if (!empty($salaryMin) && !empty($salaryMax)) {
            $query->where(function ($query) use ($salaryMin, $salaryMax) {
                $query->where('salary_min', '>=', $salaryMin)->where('salary_min', '<=', $salaryMax);
            })->orWhere(function ($query) use ($salaryMin, $salaryMax) {
                $query->where('salary_min', '<=', $salaryMin)->where('salary_max', '>=', $salaryMin);
            });
        } else if (empty($salaryMin) && !empty($salaryMax)) {
            $query->where('salary_min', '<=', $salaryMax);
        } else if (!empty($salaryMin) && empty($salaryMax)) {
            $query->where('salary_max', '>=', $salaryMin);
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

    public function scopeSearch($query, $search)
    {
        if (!empty(($search))) {
            return $query->where('name', 'like', '%'.$search.'%')->orWhere('type', 'like', '%'.$search.'%')->orWhere('location', 'like', '%'.$search.'%');
        }
    }
}
