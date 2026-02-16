<?php

namespace App\Jobs;

use App\Models\Files;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class TranscodeFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Files $files)
    {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $output_file = $this->files->getFullPath();
        $output_folder = Storage::disk('public')->path('').'segments/'.$this->files->id;
        if(!File::isDirectory($output_folder)) File::makeDirectory($output_folder, 0777, true, true);
        $generate_playlist = exec("ffmpeg -i $output_file -c:v h264_videotoolbox -x264-params keyint=1:scenecut=0 -r 30 -g 30 -c:a aac -b:v 2000k -s 1280x720 -b:a 128k -hls_list_size 0 -hls_time 10 -f hls $output_folder/playlist.m3u8");
        PrepareSegments::dispatch($this->files);
    }
}
