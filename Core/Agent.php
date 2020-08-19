<?php 

namespace GWM\Core;

/**
 * Undocumented class
 */
class Agent
{
    /**
     * Undocumented function
     *
     * @return boolean
     */
    public static function is_mobile()
    {
        return \preg_match("/(android|avantgo|blackberry|".
        "bolt|boost|cricket|docomo|fone|".
        "hiptop|mini|mobi|palm|phone|pie|".
        "tablet|up\.browser|up\.link|".
        "webos|wos)/i", $_SERVER['HTTP_USER_AGENT']);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public static function ip()
    {
        return !empty($_SERVER['HTTP_CLIENT_IP'])
        ? $_SERVER['HTTP_CLIENT_IP']
        : !empty($_SERVER['HTTP_X_FORWARDED_FOR'])
        ? $_SERVER['HTTP_X_FORWARDED_FOR']
        : $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public static function has_ajax()
    {
        $httpx_req = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
        ? $_SERVER['HTTP_X_REQUESTED_WITH'] : null;
        return (!empty($httpx_req) &&
        strtolower($httpx_req) == 'xmlhttprequest');
    }
}