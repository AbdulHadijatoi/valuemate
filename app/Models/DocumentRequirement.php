<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRequirement extends Model
{
    use HasFactory;

    protected $guarded = [
        // 'property_type_id', 'document_name'
    ];

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }
}
