<?php

namespace App\Models;

use App\Jobs\CreatePlaylist;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_id',
        'start_time',
        'end_time',
    ];

    public function fileItem()
    {
        return $this->belongsTo(Files::class,'file_id');
    }

    public static function boot()
    {
        parent::boot();
        static::created(function ($item) {
            CreatePlaylist::dispatch($item);
        });
    }
}
