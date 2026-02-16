<?php

namespace App\Jobs;

use App\Models\Events;
use App\Models\Playlists;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreatePlaylist implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Events $events)
    {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $segments = $this->events->fileItem->segments;
        $start = strtotime($this->events->start_time);
        foreach ($segments as $item) {
//            $duration = $item->duration;
            $p = new Playlists([
                'event_id' => $this->events->id,
                'segments_id' => $item->id,
                'play_time' => date('Y-m-d H:i:s',$start + $item->start),
            ]);
            $p->save();
//            $start = $start + $duration;
        }

    }
}
