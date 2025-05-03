<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValuationRequestDetail extends Model
{
    use HasFactory;

    protected $guarded = [
        // 'valuation_request_id', 'file_id'
    ];
}
