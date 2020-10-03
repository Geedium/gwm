<?php

namespace GWM\Core\Exceptions
{
    /**
     * Undocumented class
     */
    class Basic extends \Exception
    {
        /**
         * @magic
         * @param string $message
         * @param int $code
         * @param \Throwable|null $previous
         */
        public function __construct($message = "", $code = 0, \Throwable $previous = null)
        {
            parent::__construct($message, $code, $previous);
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