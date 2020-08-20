<?php

namespace GWM\Core;

/**
 * Undocumented class
 */
class Session
{
    /**
     * Undocumented function
     */
    function __construct()
    {
        \session_regenerate_id(true);
        \session_start();
    }

    /**
     * Undocumented function
     */
    function __destruct()
    {
        \session_destroy();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public static function generateToken()
    {
        $_SESSION['token'] = bin2hex(
            \base64_encode(
                \openssl_random_pseudo_bytes(32)
            )
        );
    }

    /**
     * Undocumented function
     *
     * @param [type] $token
     * @return void
     */
    public static function check($token)
    {
        if (isset($_SESSION['token']) && $token == $_SESSION['token']) {
            unset($_SESSION['token']);
            return true;
        }
        return false;
    }
}