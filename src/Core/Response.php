<?php

namespace GWM\Core;

use Latte\Engine;

/**
 * Undocumented class
 */
class Response
{
    private $content;
    private $headers;
    private $status;

    private $ob;

    const HEADER_FILE = 'Content-Disposition: attachment; filename=';

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

        if (!ob_start('ob_gzhandler')) {
            $this->ob = false;
        } else {
            $this->ob = true;
        }
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
        if (!$this->ob && headers_sent() == true) {
            die('Violation detected when sending, attempted to resend headers.');
        }

        header_remove('x-powered-by');

        Plugin::Ensure();

        foreach (Plugin::$plugins as $key) {
            $plugin = $key['ref'] ?? false;

            if ($plugin) {
                $plugin->Manipulate($this->content);
            }
        }

        foreach ($this->headers as $header) {
            header($header);
        }

        echo $this->content;

        if ($this->ob && !ob_end_flush()) {
            die('Output buffering failed.');
        }

        http_response_code($status);
        session_write_close();
        exit;
    }

    function Astray()
    {
        $html = \GWM\Core\Template\Engine::Get()
            ->Parse("themes/{$_ENV['FALLBACK_THEME']}/src/astray.html.latte");

        $this->setContent($html)->send(404);
    }

    function Redirect($url = '/', array $options = null, $status = 301)
    {
        if ($options != null) {
            $_SESSION['messages'] = [];
            $_SESSION['messages'][] = $options['message'];
        }

        if (headers_sent() == true) {
            die('Violation detected when sending, attempted to resend headers.');
        }

        ob_start();

        header("Location: $url");
        http_response_code($status);

        if (!ob_end_flush()) {
            die('Output buffering failed.');
        }

        exit;
    }

    public function sendJson($data, $status = null) : void
    {
        if (!$status) $status = $this->status;

        
        if(headers_sent() == true) {
            die('Violation detected when sending, attempted to resend headers.');
        }

        ob_get_clean();

        header('Content-type: application/json; charset=UTF-8', true, $status);
        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            // you want to allow, and if so:
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }

        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                // may also be using PUT, PATCH, HEAD etc
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         
            
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            exit(0);
        }

        ob_start();

        echo json_encode($data, JSON_PRETTY_PRINT);
        ob_flush();

        \http_response_code(200);
    }
}
