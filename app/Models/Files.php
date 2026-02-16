<?php

namespace App\Models;

use App\Jobs\TranscodeFile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Files extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'duration',
        'path',
    ];

    public function segments()
    {
        return $this->hasMany(Segments::class,'file_id')->orderBy('position','ASC');
    }

    public function getFullPath()
    {
       return Storage::disk('public')->path($this->path);
    }

    public static function boot()
    {
        parent::boot();
        static::created(function ($item) {
            TranscodeFile::dispatch($item);
        });
    }
}
