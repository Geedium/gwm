<?php

namespace GWM\Core;

/**
 * Undocumented class
 */
class Response
{
    private $content;
    private $headers;
    private $status;

    /**
     * @magic
     */
    public function __construct()
    {
        $this->content = <<<EOF
        <html>
            <body>
                Bad Request.
            </body>
        </html>
EOF;

        $this->status = 400;

        $this->headers = [
            'X-Frame-Options: SAMEORIGIN',
            'X-XSS-Protection: 1; mode=block',
            'X-Content-Type-Options: nosniff'
        ];
    }

    /**
     * Undocumented function
     *
     * @param string $data
     * @return Response
     */
    public function setContent(string $data) : Response
    {
        $this->content = $data;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $headers
     * @return Response
     */
    public function setHeaders(array $headers) : Response
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param integer $status
     * @return Response
     */
    public function setStatus(int $status) : Response
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param integer $status
     * @return void
     */
    public function send($status = null) : void
    {
        if(!$status) $status = $this->status;

        foreach ($this->headers as $header) {
            header($header, true, $status);
        }

        ob_start();
        echo $this->content;
        ob_end_flush();
    }
}

?>
