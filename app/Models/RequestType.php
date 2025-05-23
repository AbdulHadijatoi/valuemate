<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestType extends Model
{
    protected $fillable = [
        'name',
    ];

    public $timestamps = false; // Disable created_at and updated_at
}
