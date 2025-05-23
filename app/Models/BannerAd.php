<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannerAd extends Model
{
    use HasFactory;

    const DAILY_AD_TYPE = 1;
    const WEEKLY_AD_TYPE = 2;
    const MONTHLY_AD_TYPE = 3;
    const PERMANENT_AD_TYPE = 4;

    protected $guarded = [
        // 'title', 'file_id', 'link', 'start_date', 'end_date', 'ad_type', 'description'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    protected $with = ['banner'];

    public function banner()
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }
    
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }
}
