<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $guarded = [
        // 'key', 'value'
    ];

    // Fetch the record with the given key and return the value
    public static function getValue(string $key) {
        return self::where('key', $key)->value('value');
    }
}
