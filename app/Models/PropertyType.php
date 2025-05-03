<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyType extends Model
{
    use HasFactory;

    protected $guarded = [
        // 'name'
    ];

    public function documentRequirements()
    {
        return $this->hasMany(DocumentRequirement::class);
    }
}
