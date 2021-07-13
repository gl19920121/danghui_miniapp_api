<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobUser extends Model
{
    use HasFactory;

    protected $table = "job_user";
    protected $fillable = [];
    protected $guarded = [];

    public function scopeCollect($query, $userId, $jobId)
    {
        return $query->where('user_id', $userId)->where('job_id', $jobId)->where('type', 'collect');
    }
}
