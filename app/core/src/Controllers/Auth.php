<?php

namespace GWM\Core\Controllers;

class Auth extends \GWM\Core\Controller
{
    public function index()
    {
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