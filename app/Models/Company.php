<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $guarded = [
        // 'name', 'status', 'logo_file_id'
    ];

    protected $with = ['logo', 'companyDetails'];

    public function logo()
    {
        return $this->belongsTo(File::class, 'logo_file_id');
    }

    public function companyDetails()
    {
        return $this->hasOne(CompanyDetail::class, 'company_id');
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
