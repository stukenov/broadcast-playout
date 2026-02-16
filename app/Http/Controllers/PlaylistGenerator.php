<?php

namespace App\Http\Controllers;

use App\Models\Playlists;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class PlaylistGenerator extends Controller
{

    public function index(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/vnd.apple.mpegurl');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $content = "#EXTM3U" . PHP_EOL;
        $content .= "#EXT-X-VERSION:3" . PHP_EOL;
        $content .= "#EXT-X-TARGETDURATION:10" . PHP_EOL;

        $unixtimestamp = substr(time(), 0, -1); // Add 6 hours to the current time


        $content2 = '';
        $items = Playlists::whereRaw('play_time >= NOW()')->orderBy('play_time', 'ASC')->limit(3)->get();
        foreach ($items as $item) {
//            if ($content2 == '') {
//                $t = strtotime($item->play_time);
//            }
            $content2 .= "#EXTINF:" . number_format($item->segmentItem->duration, 6) . "," . PHP_EOL;
            $content2 .= "/storage/segments/" . $item->segmentItem->fileItem->id . "/" . $item->segmentItem->name . PHP_EOL;
        }
//        $t = Cache::get('key', 1);
//        Cache::increment('key');

        $content .= "#EXT-X-MEDIA-SEQUENCE:" . $unixtimestamp . PHP_EOL;
        $content .= $content2;


        $response->setContent($content);
        return $response;
    }
}
