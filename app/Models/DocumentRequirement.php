<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRequirement extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }
}
