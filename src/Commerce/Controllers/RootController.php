<?php

namespace GWM\Commerce\Controllers;

use GWM\Core\ {
    Schema,
    Response
};

/**
 * Root Controller
 * 
 * Conjunction with routes.
 * 
 * @package Commerce
 * @version 1.1.0
 */
class RootController
{
    public function listProducts(Response $response)
    {
        $data = new Schema($_ENV['DB_NAME']);
        
        $stmt = $data->prepare("SELECT * FROM ${_ENV['DB_PREFIX']}_products");
        $stmt->execute();

        $r = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $response->sendJson([
            $r
        ]);
    }
}