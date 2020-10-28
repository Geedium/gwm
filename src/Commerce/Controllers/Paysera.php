<?php

namespace GWM\Commerce\Controllers {
    class Paysera
    {
        public function pay()
        {
            $api = new \Paysera_WalletApi('', '',
                \Paysera_WalletApi_Util_Router::createForSandbox());



            var_dump($api);
            exit;
        }
    }
}