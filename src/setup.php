<?php

defined('GWM') || exit;

use GWM\Core\Response;
use GWM\Core\Schema;

use GWM\Core\Errors\Basic as GWMException;
use GWM\Core\Template\Engine as TemplateEngine;

final class Setup 
{
    static function fallback_request(?string $inform, int $status = 418) {
        $res = new Response();

        $html = TemplateEngine::Get()->Parse('resources/templates/setup/pages/401.latte', [
            'heading' => $inform
        ]);

        $res->setContent($html)->send($status);
        exit;
    }
}

function checkAuthority($key)
{
    if (file_exists('INSTALL.txt')) {
        if(!$key) { 
            Setup::fallback_request('Pass in generated installation key in query string to verify ownership.', 401);
        }
 
        $target_key = file_get_contents('INSTALL.txt');

        if ($target_key !== $key) {
            Setup::fallback_request('Unable to validate admin key.', 401);
            return false;
        }

        return true;
    } else {
        if (!file_put_contents('INSTALL.txt', bin2hex(random_bytes(16)))) {
            Setup::fallback_request('Unable to put admin key in text file.');
        }

        Setup::fallback_request('Pass in generated installation key in query string to verify ownership.', 401);
    }
}

function fallbackRoute() {
    $html = TemplateEngine::Get()->Parse('resources/templates/setup/pages/index.latte', [

    ]);

    $res = new Response();
    $res->setContent($html, Response::HTTP_OK)->send();
}

function configureEnvironment($key, $required = [])
{
    $action = filter_input(INPUT_GET, 'action');

    if (checkAuthority($key)) {
        if ($action == 'audit') {

            $data = $_POST['env'];
            $vars = '';

            foreach ($data as $key => &$value) {
                $value = \filter_var($value, \FILTER_SANITIZE_STRING);

                if($value && strlen($value) > 0) {
                    $vars .= "$key=\"".$value."\"".PHP_EOL;
                }
            }

            if (file_put_contents('.env', $vars)) {
                // Unlink install
                if (file_exists('INSTALL.txt')) {
                    unlink('INSTALL.txt');
                }
            }

            $res = new Response();
            $res->Redirect('/');
        }

        $extensions = [
            'pdo_mysql' => extension_loaded('pdo_mysql'),
            'curl' => extension_loaded('curl')
        ];
        
        try {
            $schema = new Schema();
            $isConnected = true;
        } catch (GWMException $e) {
            $isConnected = false;
        }

        $vars = [];
        foreach ($required as $var) {
            if($var == 'DB_PREFIX') {
                $vars[$var] = bin2hex(random_bytes(4));
                continue;
            }
            
            $vars[$var] = false;
        }

        $html = TemplateEngine::Get()->Parse('resources/templates/setup/pages/env.latte', [
            'exts' => $extensions,
            'required' => $vars,
            'master' => $isConnected,
            'url' => "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s" : "") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
        ]);

        $res = new Response();
        $res->setContent($html, Response::HTTP_OK)->send();
    }
}
