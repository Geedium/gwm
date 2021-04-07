<?php

namespace GWM\Commerce\Controllers;

use GWM\Core\ {
    Schema,
    Response
};

use GWM\Commerce\Models\Order;

class OrdersController
{
    function listAction()
    {
        Schema::$PRIMARY_SCHEMA = $schema = new Schema($_ENV['DB_NAME']);

        $facade = new \GWM\Core\Facades\UserFacade($schema);
        $user = $facade->construct();

        $auth = new \GWM\Core\Services\Auth($user);

        if(!$auth->is_logged())
        {
            (new Response())->Redirect('/');
        }


        $stmt = $schema->prepare("SELECT 1 FROM ${_ENV['DB_PREFIX']}_customers c
            WHERE c.user = :user");
        $stmt->execute([':user' => $user->id]);
        $customer = (int)$stmt->fetchColumn(0);

        if ($customer > 0) {
            $stmt = $schema->prepare("SELECT * FROM ${_ENV['DB_PREFIX']}_orders o
            WHERE o.customer_id = :id");
            $stmt->execute([':id' => $customer]);
            $orders_assoc = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        $orders = new \stdClass();
        foreach ($orders_assoc as $key => $value) {
            $order = new \stdClass();

            foreach ($value as $k => $v) {
                $order->{$k} = $v;

                if ($k == 'method') {
                    $order->{'links'}['download'] = 'https://';
                }
            
            }
            
            $orders->{$key} = $order;
        }

        $html = \GWM\Core\Template\Engine::Get()
            ->Parse('res/geedium.theme.bundle/src/store/orders.html.latte', array_merge(
                \GWM\Core\Controllers\Home::ContextChain(), [
                    'orders' => $orders ?? null
            ])
        );

        (new Response())->setContent($html)->send(200);
    }
}