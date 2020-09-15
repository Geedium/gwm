<?php

namespace GWM\Core\Controllers;

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
}