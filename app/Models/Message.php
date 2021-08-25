<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Auth;
use App\Models\User;
use App\Models\JobPublisher;
use App\Models\Mould;
use App\Models\Job;

class Message extends Model
{
    use HasFactory;

    protected $perPage = 3;

    protected $casts = [
        'type' => 'integer',
        'content' => 'json',
        'is_read' => 'boolean',
        'created_at' => 'datetime:Y/m/d h:m',
        'updated_at' => 'datetime:Y/m/d h:m',
    ];

    protected $fillable = [ 'is_read' ];

    protected $appends = [ 'created_at_month' ];

    public function sender()
    {
        // return $this->setConnection('mysql_crm')->belongsTo(User::class, 'send_uid');
        return $this->belongsTo(JobPublisher::class, 'send_uid');
    }

    public function job()
    {
        return $this->belongsTo(Job::class, 'content.job_id');
    }

    public function mould()
    {
        return $this->belongsTo(Mould::class);
    }

    public function getCreatedAtMonthAttribute(): String
    {
        return Carbon::parse($this->created_at)->format('m/d');
    }

    public function scopeAccept($query, Int $type)
    {
        return $query->where('accept_uid', Auth::user()->id)->where('type', $type);
    }

    public function scopeAcceptBroadcast($query)
    {
        return $query->where('accept_uid', Auth::user()->id)->where('type', 0);
    }

    public function scopeAcceptNotice($query)
    {
        return $query->where('accept_uid', Auth::user()->id)->where('type', 1);
    }

    public function scopeAcceptChat($query)
    {
        return $query->where('accept_uid', Auth::user()->id)->where('type', 2);
    }

    public function scopeAcceptUnread($query)
    {
        return $query->where('accept_uid', Auth::user()->id)->where('is_read', false);
    }

    public function scopeAcceptBroadcastUnread($query)
    {
        return $query->where('accept_uid', Auth::user()->id)->where('type', 0)->where('is_read', false);
    }

    public function scopeAcceptNoticeUnread($query)
    {
        return $query->where('accept_uid', Auth::user()->id)->where('type', 1)->where('is_read', false);
    }

    public function scopeAcceptChatUnread($query)
    {
        return $query->where('accept_uid', Auth::user()->id)->where('type', 2)->where('is_read', false);
    }
}
