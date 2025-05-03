<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $guarded = [
        // 'name', 'location_id'
    ];
    
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
