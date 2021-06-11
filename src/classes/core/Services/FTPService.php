<?php

namespace GWM\Core\Services {

    use GWM\Core\Models\User;

    /**
     * FTP Service
     * 
     * No description.
     * 
     * @version 1.1.0
     */
    class FTPService
    {
        /** @magic */
        public function __construct(User $user) {
            $ftp_connection = \ftp_connect('localhost') || die('Unable to connect.');
            ftp_login($ftp_connection, 'user', 'pass') || die("Couldn't login.");
            ftp_pasv($ftp_connection, false);
            ftp_close($ftp_connection);
        }
    }
}