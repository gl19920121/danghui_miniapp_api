<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Company extends Model
{
    use HasFactory;

    protected $connection = 'mysql_crm';

    protected $casts = [
        'industry' => 'json',
        'location' => 'json',
    ];

    protected $appends = [
        'industry_show', 'investment_show', 'scale_show', 'logo_url',
    ];

    public function getIndustryShowAttribute(): string
    {
        return $this->industry['th'];
    }

    public function getInvestmentShowAttribute(): string
    {
        return config('lang.company.investment')[$this->investment];
    }

    public function getScaleShowAttribute(): string
    {
        return config('lang.company.scale')[$this->scale];
    }

    public function getLogoUrlAttribute(): string
    {
        if (empty($this->attributes['logo'])) {
            return '';
        } else {
            return asset(Storage::disk('company_logo')->url($this->attributes['logo']));
        }
    }
}
