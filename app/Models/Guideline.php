<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guideline extends Model
{
    protected $guarded = [
        // 'title', 'description', 'type'
    ];

    /**
     * Get the type of the guideline.
     *
     * @return string
     */
    public function getTypeAttribute($value)
    {
        return ucfirst(str_replace('_', ' ', $value));
    }

    // not time stamp
    public $timestamps = false;
}
