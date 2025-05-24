<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $guarded = [
        // 'payment_method_id', 'amount', 'status', 'payment_reference'
    ];

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function valuationRequest()
    {
        return $this->belongsTo(ValuationRequest::class);
    }
}
