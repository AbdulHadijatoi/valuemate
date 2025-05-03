<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValuationRequest extends Model
{
    use HasFactory;

    protected $guarded = [
        // 'company_id', 'client_id', 'status_id', 'property_type_id', 'area_id', 'location_id', 'pricing_rule_id', 'total_amount'
    ];
}
