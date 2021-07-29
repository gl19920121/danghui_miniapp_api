<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        'avatar_url',
    ];

    public function getAvatarUrlAttribute(): string
    {
        if (empty($this->attributes['avatar'])) {
            return '';
        } else {
            return asset(Storage::disk('user_avatar')->url($this->attributes['avatar']));
        }
    }
}
