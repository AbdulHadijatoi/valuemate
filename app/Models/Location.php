<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $guarded = [
        // 'name'
    ];

    // append map url attribute and create it from latitude and longitude
    protected $appends = ['map_url'];
    
    public function getMapUrlAttribute()
    {
        if ($this->latitude && $this->longitude) {
            return "https://maps.google.com/?q={$this->latitude},{$this->longitude}";
        }
        return null;
    }
}
