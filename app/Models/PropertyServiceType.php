<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyServiceType extends Model
{
    protected $guarded = [
        // 'property_type_id',
        // 'service_type_id',
    ];

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }
}
