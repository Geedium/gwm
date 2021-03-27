<?php

namespace GWM\Commerce\Controllers {
    /**
     * Class Checkout
     */
    class Checkout
    {
        function index()
        {
            $html = \GWM\Core\Template\Engine::Get()
            ->Parse('resources/{b186213c-38ba-47ad-b2e4-f235acabd0d0}/templates/checkout.latte', [
                'paypal_url' => ''
            ]);
        }
    }
}