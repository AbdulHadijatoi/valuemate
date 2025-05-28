<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory;

    protected $guarded = [
        // 'path', 'type'
    ];

    // append full path to the model
    protected $appends = ['full_path'];
    public function getFullPathAttribute()
    {
        return url(Storage::url($this->path));
    }

    public function saveFile($file)
    {
        $path = $file->store('files');
        $this->path = $path;
        $this->type = $file->getClientMimeType();
        $this->save();

        // return the file path
        return $this->path;
    }
}
