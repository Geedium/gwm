<?php

namespace GWM\Core\Exceptions
{
    /**
     * Undocumented class
     */
    class Basic extends \Exception
    {
        public function errorMessage() 
        {
            return $this->getMessage();
        }
    }
}