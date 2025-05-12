<?php

namespace App\Models;

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
        // 'title', 'file_id', 'link', 'start_date', 'end_date', 'ad_type'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];
}
