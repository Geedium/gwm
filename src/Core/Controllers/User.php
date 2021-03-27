<?php

namespace GWM\Core\Controllers {

    use GWM\Core\ {
        Response,
        Session
    };

    class User
    {
        public function Profile(): void
        {
            $response = new Response();

            $html = \GWM\Core\Template\Engine::Get()
            ->Parse('themes/default/templates/profile.latte', [
                'username' => Session::Get()->Username()
            ]);

            $response->setContent($html)->send(200);
        }
    }
}