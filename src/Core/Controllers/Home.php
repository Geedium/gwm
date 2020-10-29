<?php

namespace GWM\Core\Controllers;

use GWM\Core\Excel;
use GWM\Core\Utils\Form;
use GWM\Core\Utils\Table;

class Home
{
    private function _substr( $str, $start, $length = null ) {
        return ( ini_get( 'mbstring.func_overload' ) & 2 ) ? mb_substr( $str, $start, ( $length === null ) ? mb_strlen( $str, '8bit' ) : $length, '8bit' ) : substr( $str, $start, ( $length === null ) ? strlen( $str ) : $length );
    }

    public function index()
    {
        if(session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }

        // XLS START

        $file = GWM['DIR_ROOT'].'\\Phonebook.xlsx';
        echo 'Exists: '.file_exists($file).'<br/>';
        echo 'Read: '.is_readable($file).'<br/>';
        echo 'Write: '.is_writable($file).'<br/>';

        $excel = new Excel('Phonebook');

        if($excel->load()) {
            echo '<pre>';
            print_r($excel->data);
            echo '</pre>';
        } else {
            print_r(error_get_last());
        }

        // XLS END

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

        $table->Handle($schema);

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