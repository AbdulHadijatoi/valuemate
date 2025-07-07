<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValuationRequest extends Model
{
    use HasFactory;

    protected $with = [
        "company",
        "user",
        "status",
        "propertyType",
        "serviceType",
        "requestType",
        "location",
        "servicePricing",
        "documents",
    ];

    protected $guarded = [
        // "company_id",
        // "user_id",
        // "status_id",
        // "property_type_id",
        // "service_type_id",
        // "request_type_id",
        // "location_id",
        // "service_pricing_id",
        // "area_from",
        // "area_to",
        // "total_amount",
        // "reference",
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function status()
    {
        return $this->belongsTo(ValuationRequestStatus::class);
    }
    
    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }
    
    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }
    
    public function requestType()
    {
        return $this->belongsTo(RequestType::class);
    }
    
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
    
    public function servicePricing()
    {
        return $this->belongsTo(ServicePricing::class);
    }

    public function documents()
    {
        return $this->hasMany(ValuationRequestDocument::class, 'valuation_request_id');
    }
    
    public function payments()
    {
        return $this->hasMany(Payment::class, 'valuation_request_id')->orderBy('id','desc');
    }
    
    public function lastPayment()
    {
        return $this->hasOne(Payment::class, 'valuation_request_id')->latest('id');
    }
}
