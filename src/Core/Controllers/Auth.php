<?php

namespace GWM\Core\Controllers;

class Auth extends \GWM\Core\Controller
{
    public function index()
    {
        $schema = new \GWM\Core\Schema('test_app');

        $user_input = \filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $pass_input = \filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

        $model = new \GWM\Core\Models\User($schema);

        if (\filter_has_var(INPUT_POST, 'username')) {
            $model->setUserName($user_input);
        }

        if (\filter_has_var(INPUT_POST, 'password')) {
            $model->setPassword($pass_input);
        }

        return $this->register( $user_input, $model->getPassword() );
    }
    
    private function register($a, $b)
    {
        $latte = new \Latte\Engine;
        $latte->setTempDirectory('tmp/latte');

        session_start();
        if (empty($_SESSION['token'])) {
            $_SESSION['token'] = bin2hex(random_bytes(32));
        }
        $token = $_SESSION['token'];

        $params = [
            'user' => $a,
            'pass' => $b,
            'csrf' => $token
        ];

        $latte->render('themes/default/templates/auth.latte', $params);
    }
}