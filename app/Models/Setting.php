<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    public $timestamps = false; // Disable created_at and updated_at

    protected $primaryKey = 'key'; // Set primary key
    public $incrementing = false; // Prevent auto-increment assumption
    protected $keyType = 'string'; // Because 'key' is a string

    protected $guarded = [
        // 'key', 'value'
    ];

    // Fetch the record with the given key and return the value
    public static function getValue(string $key) {
        return self::where('key', $key)->value('value');
    }
}
