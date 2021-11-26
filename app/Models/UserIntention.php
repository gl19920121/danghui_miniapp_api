<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class UserIntention extends Model
{
    use HasFactory;

    public const MAX_SIZE = 3;

    protected $casts = [
        'position' => 'json',
        'industry' => 'json',
        'salary' => 'json',
        'created_at' => 'datetime:Y-m-d h:m:s',
        'updated_at' => 'datetime:Y-m-d h:m:s',
    ];

    protected $fillable = [
        'type', 'city', 'position', 'industry', 'salary', 'user_id',
    ];

    protected $appends = [
        'position_show', 'industry_show', 'salary_show',
    ];

    public function getPositionShowAttribute(): string
    {
        return $this->position['rd'];
    }

    public function getIndustryShowAttribute(): Array
    {
        return empty($this->industry) ? ['行业不限'] : array_column($this->industry, 'th');
    }

    public function getSalaryShowAttribute(): string
    {
        if (empty($this->salary)) {
            return '兼职';
        } else if (isset($this->salary['negotiation']) && $this->salary['negotiation']) {
            return '面议';
        } else {
            return sprintf('%s-%sK', $this->salary['min'], $this->salary['max']);
        }
    }

    public function scopeMine($query)
    {
        return $query->where('user_id', Auth::user()->id);
    }
}
