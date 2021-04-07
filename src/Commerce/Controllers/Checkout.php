<?php

namespace GWM\Commerce\Controllers {

    use GWM\Core\ {
        Schema,
        Response
    };

    use GWM\Commerce\Models\Order;

    use GWM\Commerce\Controllers\Escrow as EscrowController;

    use GWM\Commerce\Cart;

    use SQL;

    /**
     * Class Checkout
     */
    class Checkout
    {
        /** @DI_Route */
        function create_order(Response $response)
        {
            $transaction = new EscrowController();

            $details = $transaction->create_transaction();
            $schema = Schema::$PRIMARY_SCHEMA = new Schema($_ENV['DB_NAME']);

            //
            $user = new \GWM\Core\Facades\UserFacade();
            $user_model = $user->construct();

            $auth = new \GWM\Core\Services\Auth($user_model);

            if(!$auth->is_logged())
            {
                $response->Redirect('/cart', [
                    'message' => 'You must be logged in to checkout.'
                ]);
            }

            $is_customer_sql = (new Sql())
            ->select([1])
            ->from(env('DB_PREFIX', function($key) {
                echo 'ENV Failed! > '.$key.exit();
            }).'_'.'customers', 'c')
            ->where('c.user = ?');

            $stmt = $schema->prepare($is_customer_sql);
            $stmt->execute([$user_model->id]);
            
            $r = $stmt->fetchColumn(0);

            if(!$r) {

                $stmt = $schema->prepare("INSERT INTO ${_ENV['DB_PREFIX']}_customers
                    (user) VALUES (?)");

                $r = $stmt->execute([$user_model->id]);

                if (!$r) {
                    exit;
                }
            }

            $order = new Order();
            $order->_INIT($schema);
            $order->txn_id = $details->id;

            $stmt = $schema->prepare("INSERT INTO ${_ENV['DB_PREFIX']}_orders
                (txn_id, method, customer_id) VALUES (?, ?, ?)");

            $stmt->execute([
                $order->txn_id,
                'escrow',
                $user_model->id
            ]);

            $params = array_merge(\GWM\Core\Controllers\Home::ContextChain(), [
                 'order' => $details 
            ]);

            $html = \GWM\Core\Template\Engine::Get()
                ->Parse("res/{$_ENV['FALLBACK_THEME']}/src/store/escrow_agree.html.latte", $params);

            $response->setContent($html)->send(200);
        }

        function index()
        {
            $schema = Schema::$PRIMARY_SCHEMA = new Schema($_ENV['DB_NAME']);
            $cart = Cart::Get();
            $target_currency = 'EUR';

            $keys = array_keys($_SESSION['cart']);
            $values = array_values($_SESSION['cart']);

            $in  = str_repeat('?,', count($keys) - 1) . '?';

            $stmt = $schema->prepare("SELECT p.*
                FROM ${_ENV['DB_PREFIX']}_products p
                WHERE p.id IN ($in)");

            $stmt->execute($keys);
            $products = $stmt->fetchAll(\PDO::FETCH_CLASS, \GWM\Commerce\Models\Product::class);

            $total_value = 0.0;

            $api = json_decode(file_get_contents("https://api.coindesk.com/v1/bpi/currentprice/EUR.json"));

            foreach ($products as &$product) {
                $price = (float)rtrim($product->price, '€');

                !is_numeric($price) && exit;

                if (!$product->image) {
                    $product->image = 'images/no-image-scaled.png';
                }
                
                /**
                 * --------------------------\
                 * QUANTITY ENSUREMENT       |
                 * --------------------------\
                 */
                $quantity = (int)array_pop($values)['quantity'];
                
                if ($quantity > $product->stock) 
                {
                    $quantity = $product->stock;
                } 
                else if($quantity < $product->stock && $quantity < 0)
                {
                    $quantity = 0;
                }

                /**
                 * --------------------------\
                 * CURRENCY CONVERSION       |
                 * --------------------------\
                 */
                if ($target_currency == 'BTC') {
                    $exchange_rate = $api->bpi->EUR->rate_float;
                    $unit = 1.0 / $exchange_rate;

                    $product->{'unit_price'} = number_format($price * $unit, 8);
                    $product->price = number_format($price * $unit * $quantity, 8);
                    $product->{'symbol'} = \html_entity_decode("&#8383;");
                    $product->{'cart_quantity'} = $quantity;

                    $total_value += $product->price;
                }
            }

            switch ($target_currency) {
                case 'EUR':
                    $cart->{'symbol'} = html_entity_decode("&euro;");
                break;
                case 'BTC':
                    $api = json_decode(file_get_contents("https://api.coindesk.com/v1/bpi/currentprice/EUR.json"));
                    $exchange_rate = $api->bpi->{$cart->currency}->rate_float;
                    $unit = 1.0 / $exchange_rate;

                    $cart->{'symbol'} = \html_entity_decode("&#8383;");
                    $cart->total = number_format($total_value, 8);
                break;
            }
            
            $params = array_merge(\GWM\Core\Controllers\Home::ContextChain(), [
                'cart' => $cart,
                'total_quantity' => $cart->Total(),
                'total_value' => $total_value,
                'products' => $products
            ]);

            $html = \GWM\Core\Template\Engine::Get()
                ->Parse("res/{$_ENV['FALLBACK_THEME']}/src/store/checkout.html.latte",  $params);

            $response = new Response();
            $response->setContent($html)->send(200);
        }
    }
}