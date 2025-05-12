<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $guarded = [
        // 'path', 'type'
    ];

    public function getPathAttribute($value)
    {
        return url('storage/' . $value);
    }

    public function saveFile($file)
    {
        $path = $file->store('files', 'public');
        $this->path = $path;
        $this->type = $file->getClientMimeType();
        $this->save();
    }
}
