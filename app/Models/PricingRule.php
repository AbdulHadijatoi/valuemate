<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingRule extends Model
{
    use HasFactory;

    protected $guarded = [
        // 'property_type_id', 'area_range', 'price'
    ];

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }
}
