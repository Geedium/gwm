<?php

namespace GWM\Core\Controllers;

use GWM\Core\Response;

class Cache
{
    public function index($days, $response)
    {
        $expires = 3600 * 24 * $days;
        $datetime = gmdate('D, d M Y H:i:s', time() + $expires);

        $response->setHeaders([
            "Cache-Control: maxage=$expires",
            "Expires: $datetime GMT"
        ]);
    }

    public function png($args)
    {
        if(session_status() != PHP_SESSION_ACTIVE) {
            session_cache_expire(60);
            session_cache_limiter("public");
            session_start();
        }

        $arg = $args['pux.route'][3]['vars'][1];
        $filename = GWM['DIR_PUBLIC'] . "/images/$arg.png";

        if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified', true);
            header('Pragma: cache', true);
            http_response_code(304);
            exit;
        }

        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) &&
            strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= filemtime($filename)) {

            header($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified', true);
            header('Pragma: cache', true);
            http_response_code(304);
            exit;
        }

        $response = new Response();

        $imagedata = imagecreatefrompng($filename);

        $stream = fopen('php://memory', 'r+');
        imagepng($imagedata, $stream);
        rewind($stream);
        $stringdata = stream_get_contents($stream);

        if (!$stringdata) {
            $response->send(204);
            exit;
        }

        $response->setContent($stringdata);

        $response->setHeaders([
            'Pragma: public',
            'Cache-Control: max-age=86400, public',
            'Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 86400),
            'Last-Modified: ' . gmdate('D, d M Y H:i:s \G\M\T', filemtime($filename)),
            'Content-Type: image/png',
        ]);

        $response->send();
        exit;
    }
}