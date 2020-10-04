<?php

namespace GWM\Core\Controllers;

class Home
{
    public function index()
    {
        $schema = new \GWM\Core\Schema('test_app');

        $model = new \GWM\Core\Models\Article($schema);
        $articles = $model->Select($schema);

        $latte = new \Latte\Engine;
        $latte->setTempDirectory('tmp/latte');

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        } else {
            session_start();
            session_regenerate_id();
        }

        $user = 'undefined';
        $pass = 'undefined';

        if(isset($_SESSION['user'])) {
            $user = $_SESSION['user'];
            // setcookie('user', $user, time() + 3600, '/', false, true, []);
        }

        $params = [
            'articles' => $articles,
            'user' => $user,
            'pass' => $pass
        ];

        $latte->render('themes/default/templates/index.latte', $params);
    }
}