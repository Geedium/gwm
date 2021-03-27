<?php

namespace GWM\Core {
    /**
     * Portable Document Format
     * @package GWM\Core
     */
    class PDF {
        private $response;

        public function __construct(Response $response)
        {
            $this->response = $response;
        }

        public function Load($bin)
        {
            $header = unpack('c*', $bin);
            $header[2] = chr($header[2]);
            $header[3] = chr($header[3]);
            $header[4] = chr($header[4]);

            if($header[2] == 'P' && $header[3] == 'D' && $header[4] == 'F') {
                // This is a PDF file.
            } else {
                // Nope.
            }
        }

        /**
         * Send
         */
        public function Send()
        {
            $this->response->setHeaders([
                'Content-type: application/pdf',
                'Content-Disposition: inline; filename=?',
                'Content-Transfer-Encoding: binary',
                'Accept-Ranges: bytes'
            ])->send();
        }
    }
}