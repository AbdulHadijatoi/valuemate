<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $guarded = [
        // 'user_id', 'message', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function getCreatedAtAttribute($value)
    {
        if($value == null) {
            return null;
        }
        return \Carbon\Carbon::parse($value)->diffForHumans();
    }
}
