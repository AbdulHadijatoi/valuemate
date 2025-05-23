<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValuationRequestDocument extends Model
{
    use HasFactory;

    protected $guarded = [
        // 'valuation_request_id', 'file_id'
    ];

    protected $with = ['document','documentRequirement'];

    public function document()
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    public function documentRequirement()
    {
        return $this->belongsTo(DocumentRequirement::class, 'document_requirement_id');
    }
}
