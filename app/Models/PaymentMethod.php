<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    public $timestamps = false; // Disable created_at and updated_at
    
    protected $guarded = [
        // 'name'
    ];

    public function logo()
    {
        return $this->belongsTo(File::class, 'file_id');
    }
}
