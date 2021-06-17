<?php

namespace GWM\Core\Errors
{
    use GWM\Core\Utils\Debug;

    use function fastcgi_finish_request;

    /**
     * Undocumented class
     */
    class Basic extends \Exception
    {
        public function __construct($message = "", $fatal = false)
        {
            if ($fatal) {
                echo <<<ERROR
                
<style>
.die {
    background: red;
    color: white;
    padding: 6px;
    border: 1px solid #c44242;
}
</style>
                
                <div class="die">

ERROR;

                if (\function_exists('fastcgi_finish_request')) {
                    fastcgi_finish_request();
                }

                die("[$this->line, $this->file]: " . $message . '</div>');
            }

            Debug::$log[] = "[$this->line, $this->file]: " . $message;

            parent::__construct($message);
        }

        /**
         * @magic
         * @return string
         */
        function __toString()
        {
            return __CLASS__ . ": [{$this->line}]: {$this->message}\n";
        }
    }
}