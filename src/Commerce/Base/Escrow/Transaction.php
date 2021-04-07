<?php

namespace GWM\Commerce\Base\Escrow {
    class Transaction implements \Id92db41814
    {
        private $curl, $ca;

        private function __construct() 
        {
            $this->curl = \curl_init();

            if (!$this->curl) {
                throw new \Exception('Unable to initialize cURL!');
            }

            $this->ca = GWM['DIR_ROOT'].'/certs/ca-certificates.crt';

            \curl_setopt_array($this->curl, [
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_CAINFO => $this->ca,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 1,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json'
                ],
            ]);
        }

        function __destruct()
        {
            $this->Dispose();
        }

        public function Dispose(): void
        {
            \curl_close($this->curl);
        }
        
        public static function Get(): self
        {
            return new self;
        }

        public function Auth($user, $pass): self {
            curl_setopt($this->curl, CURLOPT_USERPWD, "$user:$pass");
            return $this;
        }

        public function Create(&$_OUT, array $data)
        {
            \curl_setopt_array($this->curl, [
                CURLOPT_URL => 'https://api.escrow-sandbox.com/2017-09-01/transaction',
                CURLOPT_POSTFIELDS => json_encode($data)
            ]);

            $_OUT = curl_exec($this->curl);

            return $this;
        }

        public function Retrieve(&$_OUT, $id)
        {
            \curl_setopt_array($this->curl, [
                CURLOPT_URL => 'https://api.escrow-sandbox.com/2017-09-01/transaction/'.$id,
            ]);

            $_OUT = \curl_exec($this->curl);

            return $this;
        }
    }
}