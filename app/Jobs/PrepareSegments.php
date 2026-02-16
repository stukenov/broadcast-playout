<?php

namespace App\Jobs;

use App\Models\Files;
use App\Models\Segments;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class PrepareSegments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Files $files)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $output_folder = Storage::disk('public')->path('').'segments/'.$this->files->id;

        $m3u8_file = $output_folder . "/playlist.m3u8";
        $lines = file($m3u8_file);

        $segment_number = 0;
        $segment_start = 0;
        $segment_end = 0;
        $duration = 0;
        foreach ($lines as $line) {
            if (substr($line, 0, 7) === "#EXTINF") {
                $duration = floatval(substr($line, 8));
            } elseif (substr($line, 0, 1) !== "#") {
                $segment_name = trim($line);
                $segment_number++;
                $segment_start = $segment_end;
                $segment_end = $segment_start + $duration;

                $item = new Segments([
                    'name' => $segment_name,
                    'position' => $segment_number,
                    'duration' => $duration,
                    'start' => $segment_start,
                    'end' => $segment_end,
                    'file_id' => $this->files->id,
                ]);
                $item->save();
            }
        }
        $this->files->duration = $segment_end;
        $this->files->save();
    }
}
