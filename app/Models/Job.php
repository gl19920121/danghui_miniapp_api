<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\User;
use Auth;

class Job extends Model
{
    use HasFactory;

    public const STATUS_ALL = 'all';
    public const STATUS_ACTIVE = 'active';

    protected $connection = 'mysql_crm';

    protected $hidden = [
        'release_uid', 'execute_uid', 'channel'
    ];

    protected $casts = [
        'type' => 'json',
        'location' => 'json',
        'channel' => 'array',
    ];

    protected $appends = [
        'is_collected',
    ];

    protected $perPage = 10;

    public function collects()
    {
        $connection = 'mysql';
        return $this->setConnection($connection)->belongsToMany(User::class)->wherePivot('user_id', Auth::user()->id)->wherePivot('type', 'collect')->withTimestamps();
    }

    public function getIsCollectedAttribute(): bool
    {
        return $this->collects()->count() > 0 ? true : false;
    }

    public function scopeStatus($query, $status)
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

    public function scopeSearchByCreatedAt($query, $duration)
    {
        switch ($duration) {
            case 'today':
                return $query->whereDate('created_at', '=', Carbon::today()->toDateString());
                break;
            case '3days':
                return $query->whereDate('created_at', '>', Carbon::today()->modify('-3 days')->toDateString());
                break;
            case 'aweek':
                return $query->whereDate('created_at', '>', Carbon::today()->modify('-7 days')->toDateString());
                break;
            case 'amonth':
                return $query->whereDate('created_at', '>', Carbon::today()->modify('-1 month')->toDateString());
                break;

            default:
                break;
        }
    }

    public function scopeSearchByExperience($query, $experience)
    {
        if (!empty($experience)) {
            if ($experience === 'school/fresh_graduates') {
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
}
