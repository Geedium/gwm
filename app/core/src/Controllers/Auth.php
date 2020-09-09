<?php

namespace GWM\Core\Controllers;

class Auth extends \GWM\Core\Controller
{
    public function index()
    {
        $schema = new \GWM\Core\Schema('test_app');

        $user_input = \filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $pass_input = \filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
        echo 'pass:'.$pass_input;

        $model = new \GWM\Core\Models\User($schema);

        if (\filter_has_var(INPUT_POST, 'username')) {
            $model->setUserName($user_input);
        }

        if (\filter_has_var(INPUT_POST, 'password')) {
            $model->setPassword($pass_input);
        }

        echo 'User: '.$model->getUserName();
        echo 'Pass: '.$model->getPassword();

        return $this->register();
    }
    
    private function register()
    {
        echo <<<EOF
        <form method="POST" action="/auth">
        Username:<br>
        <input type="text" name="username"><br>
        Password:<br>
        <input type="password" name="password">
        <br><br>
        <input type="submit" value="Submit">
      </form> 
EOF;
    }
}