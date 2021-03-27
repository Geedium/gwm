<?php

namespace GWM\Commerce\Controllers {

    use GWM\Core\Response;
    use GWM\Core\Session;
    use GWM\Core\Template\Engine;

    /**
     * Class Tebex
     * @package GWM\Commerce
     */
    class Tebex {
        function Payments(): void
        {
            $response = new Response();

            if (!Session::Get()->Logged()) {
                $response->Astray();
            }

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'https://plugin.tebex.io/payments');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_PORT , 443);
            curl_setopt($curl, CURLOPT_VERBOSE, 0);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_CAINFO, GWM['DIR_ROOT'].'/certs/ca-certificates.crt');
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'X-Tebex-Secret:  '.$_ENV['TEBEX_SERVER_KEY'],
                'limit: 50'
            ]);

            $json = curl_exec($curl);
            // $info =curl_errno($curl)>0 ? array("curl_error_".curl_errno($curl)=>curl_error($curl)) : curl_getinfo($curl);
            // print_r($info);

            curl_close($curl);

            $list = json_decode($json);

            try {
                $latte = Engine::Get('latte')->Parse(
                    'themes/admin/templates/d1e44488-e816-4bd5-a2da-9e1c5ff2ce73.latte',
                    array_merge(\GWM\Core\Controllers\Dashboard::Defaults(), [
                        'payments' => $list
                    ])
                );
            } catch (\Exception $e) {
                $response->Astray();
                exit(0);
            }

            $response->setContent($latte)->send();
        }
    }
}