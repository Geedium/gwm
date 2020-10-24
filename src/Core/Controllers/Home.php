<?php

namespace GWM\Core\Controllers;

use GWM\Core\Utils\Form;
use GWM\Core\Utils\Table;

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

        $form = new Form;
        $form->Hint($model);

        $table = new Table;
        $table->Hint($model);
        $table->All($articles);

        $user = 'undefined';
        $pass = 'undefined';

        if(isset($_SESSION['user'])) {
            $user = $_SESSION['user'];
            $pass = '';
            // setcookie('user', $user, time() + 3600, '/', false, true, []);
        }

        $params = [
            'articles' => $articles,
            'user' => $user,
            'pass' => $pass,
            'form' => $form,
            'table' => $table,
        ];

        $latte->render('themes/default/templates/index.latte', $params);
    }
}