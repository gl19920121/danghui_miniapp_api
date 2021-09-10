<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
// use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use App\Models\UserIntention;
use App\Models\Resume;
use App\Models\Message;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'openid', 'session_key', 'password', 'phone', 'jobhunter_status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'openid', 'session_key', 'unionid', 'password',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'jobhunter_status' => 'integer',
    ];

    protected $appends = [
        'is_signup', 'jobhunter_status_show', 'has_resume', 'resume_count', 'resume_rungs',
    ];

    public function acceptMessages()
    {
        return $this->hasMany(Message::class, 'accept_uid');
    }

    public function sendMessages()
    {
        return $this->hasMany(Message::class, 'send_uid', 'openid');
    }

    public function intention()
    {
        return $this->hasMany(UserIntention::class);
    }

    public function resumes()
    {
        return $this->hasMany(Resume::class, 'upload_uid', 'openid');
    }

    // public function getIdAttribute()
    // {
    //     return $this->openid;
    // }

    public function getIsSignupAttribute()
    {
        return empty($this->phone) ? false: true;
    }

    public function getJobhunterStatusShowAttribute(): string
    {
        return config('lang.resume.jobhunter_status')[$this->jobhunter_status];
    }

    public function getAvatarUrlAttribute(): String
    {
        if (!empty($this->attributes['avatar_url'])) {
            $url = Storage::disk('resume_avatar')->url($this->attributes['avatar']);
        } elseif ($this->gender === '1') {
            $url = 'images/avatar_default_white_man.png'; // female
        } else {
            $url = 'images/avatar_default_white_man.png';
        }

        return asset($url);
    }

    public function getHasResumeAttribute(): Bool
    {
        return $this->resumes()->count() > 0;
    }

    public function getResumeCountAttribute(): Int
    {
        return $this->resumes()->count();
    }

    public function getResumeRungsAttribute(): Int
    {
        return $this->resume_count > 0 ? $this->resumes()->first()->rungs : 0;
    }

    public function setPasswordAttribute($value)
    {
        if (strlen($value) != 60) {
            $value = bcrypt($value);
        }

        $this->attributes['password'] = $value;
    }

    public function scopeOpenid($query, $openid)
    {
        return $query->where('openid', $openid);
    }

    public function scopePhone($query, $phone)
    {
        return $query->where('phone', $phone);
    }

    public function findForPassport($username)
    {
        return $this->where('openid', $username)->first();
    }

    public function validateForPassportPasswordGrant($password)
    {
        return Hash::check($password, $this->password);
    }
}
