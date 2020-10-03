<?php

namespace GWM\Core\Controllers;

use Exception;
use GWM\Core\Controller;
use GWM\Core\Models\User;
use GWM\Core\Schema;
use Latte\Engine;
use function filter_has_var;
use function filter_input;

/**
 * Class Auth
 * @package GWM\Core\Controllers
 */
class Auth
{
    public function index()
    {
        $schema = new Schema('test_app');

        $user_input = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING, FILTER_REQUIRE_SCALAR);
        $pass_input = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING, FILTER_REQUIRE_SCALAR);

        $model = new User($schema);

        if (filter_has_var(INPUT_POST, 'username')) {
            $model->setUserName($user_input);
        }

        if (filter_has_var(INPUT_POST, 'password')) {
            $model->setPassword($pass_input);
        }

        if(filter_has_var(INPUT_POST, 'username') &&
            filter_has_var(INPUT_POST, 'password')) {
            $model->register($schema);
        }

        return $this->register( $user_input, $pass_input );
    }
    
    private function register($a, $b)
    {
        $latte = new Engine;
        $latte->setTempDirectory('tmp/latte');

        session_start();
        if (empty($_SESSION['token'])) {
            try {
                $_SESSION['token'] = bin2hex(random_bytes(32));
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }

        $csrf = hash_hmac('sha256', 'GWM_AUTH', $_SESSION['token']);

        $params = [
            'user' => '',
            'pass' => '',
            'csrf' => $csrf,
        ];

        echo 0;

        if(isset($_POST['submit'])) {
            echo 1;

            if (hash_equals($csrf, $_POST['csrf'])) {
                $params['user'] = $a;
                $params['pass'] = $b;
            }
        }

        $latte->render('themes/default/templates/auth.latte', $params);
    }
}