<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicePricing extends Model
{
    protected $guarded = [
        // 'service_type_id',
        // 'property_type_id',
        // 'company_id',
        // 'price',
        // 'currency',
    ];

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }
}
