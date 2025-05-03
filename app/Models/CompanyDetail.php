<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyDetail extends Model
{
    use HasFactory;

    protected $guarded = [
        // 'company_id', 'address', 'email', 'phone'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
