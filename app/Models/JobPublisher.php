<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class JobPublisher extends Model
{
    use HasFactory;

    protected $connection = 'mysql_crm';

    protected $table = 'users';

    protected $hidden = [
        'id', 'account', 'password', 'remember_token', 'is_admin'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:m:s',
        'updated_at' => 'datetime:Y-m-d h:m:s',
    ];

    protected $appends = [
        'avatar_url', 'job_show', 'active_on'
    ];

    public function getAvatarUrlAttribute(): string
    {
        if (empty($this->attributes['avatar'])) {
            $defaultUrl = $this->sex === '女' ? '/images/avatar_default.png' : '/images/avatar_default.png';
            return env('CRM_APP_URL') . $defaultUrl;
        } else {
            return asset(Storage::disk('user_avatar')->url($this->attributes['avatar']));
        }
    }

    public function getJobShowAttribute(): string
    {
        return !empty($this->job) ? $this->job : 'HR';
    }

    public function getActiveOnAttribute(): string
    {
        if (empty($this->login_on)) {
            $last = Carbon::parse($this->updated_at);
        }else if (empty($this->logout_on)) {
            $last = Carbon::parse($this->login_on);
        } else {
            $login = Carbon::parse($this->login_on);
            $logout = Carbon::parse($this->logout_on);
            $last = $logout > $login ? $logout : $login;
        }

        $days = (new Carbon)->diffInDays($last, true);
        if ($days > 7) {
            return $last->toDateString();
        } elseif ($days > 0) {
            return sprintf('%s天前', $days);
        }

        $hours = (new Carbon)->diffInHours($last, true);
        if ($hours > 0) {
            return sprintf('%s小时前', $hours);
        }

        $minutes = (new Carbon)->diffInMinutes($last, true);
        if ($minutes > 0) {
            return sprintf('%s分钟前', $minutes);
        }

        return '刚刚';
    }
}
