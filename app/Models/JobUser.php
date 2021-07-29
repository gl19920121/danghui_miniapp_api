<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Auth;

class JobUser extends Pivot
{
    use HasFactory;

    protected $table = "job_user";
    protected $fillable = [];
    protected $guarded = [];

    public function scopeIsCollect($query, $userId, $jobId)
    {
        return $query->where('user_id', $userId)->where('job_id', $jobId)->where('type', 'collect');
    }

    public function scopeMyCollect($query)
    {
        return $query->where('user_id', Auth::user()->id)->where('type', 'collect');
    }
}
