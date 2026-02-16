<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playlists extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'segments_id',
        'play_time',
    ];

    public function eventItem()
    {
        return $this->belongsTo(Events::class, 'event_id');
    }

    public function segmentItem()
    {
        return $this->belongsTo(Segments::class, 'segments_id');
    }
}
