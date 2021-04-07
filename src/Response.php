<?php

namespace GWM {

    use Core\Crypts\Context;
    
    use function ini_get;

    class Response
    {
        private Context $context;

        private array $headers;

        private int $status;

        private bool $output_buffering;

        public function __construct()
        {
            $this->output_buffering = ini_get('output_buffering') > 0;

            if ($this->output_buffering) {
                $this->output_buffering = ob_start('ob_gzhandler');
            }
        }

        public function set_context(Context $context): self
        {
            $this->context = $context;
            return $this;
        }

        public function set_headers(array $headers): self
        {
            array_diff($this->headers, $headers);
            return $this;
        }
        
        public function set_status(int $status): Response
        {
            $this->status = status;
            return $this->status;
        }

        private function validate(): bool
        {
            if (!$this->output_buffering || headers_sent() == true) {
                return false;
            }
            
            foreach ($this->headers as $header) {
                header($header);
            }
            
            header_remove('x-powered-by');
            return true;
        }

        public function send()
        {

        }
    }

}
