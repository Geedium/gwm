<?php

namespace GWM\Commerce\Controllers;

use GWM\Commerce\Cart;
use GWM\Commerce\Models\Category;
use GWM\Commerce\Models\Manufacturer;
use GWM\Commerce\Models\Product;
use GWM\Core\Errors\Basic;
use GWM\Core\Response;
use GWM\Core\Schema;
use GWM\Core\Session;
use GWM\Core\Template\Engine;
use GWM\Core\Utils\Agent;
use GWM\Core\Utils\Debug;

class Store
{
    public function index()
    {
        $cart = Cart::Get();

        $products = [];
        $categories = [];

        try {
            $schema = new Schema($_ENV['DB_NAME']);

            $model = new Product();
            $model->_INIT($schema);

            Schema::$PRIMARY_SCHEMA = $schema;

            $products = $schema->All(Product::class) ?? [];

            foreach($products as $product) {
                if ($product instanceof Product) {
                    $product->description = html_entity_decode($product->description);
                }
            }

            $category = new Category();
            $category->_INIT($schema);

            $categories = $schema->All(['categories', Category::class], 'ref IS NULL') ?? [];

        } catch (\ReflectionException $e) {
            Debug::$log[] = $e->getMessage();
        } catch (Basic $e) {
            Debug::$log[] = $e->getMessage();
        }

        $response = new Response();

        $span = \GWM\Core\Utils::Span($products, 'getPrice');
        $btcAPI = json_decode(file_get_contents("https://api.coindesk.com/v1/bpi/currentprice/EUR.json"));

        foreach ($products as &$product) {
            $price = (float)rtrim($product->price, '€');

            $ONE_BTC = $btcAPI->bpi->EUR->rate_float;
            $ONE_EURO = 1.0 / $ONE_BTC;
            
            $product->{"btc"} = number_format($price * $ONE_EURO, 8);

            if(!$product->image) {
                $product->image = 'images/no-image-scaled.png';
            }
        }

        $alert = $_SESSION['alert'];

        unset($_SESSION['alert']);

        $html = \GWM\Core\Template\Engine::Get()
            ->Parse("res/{$_ENV['FALLBACK_THEME']}/src/store/index.html.latte", [
                'cart' => $cart->Total(),
                'products' => $products,
                'categories' => $categories,
                'username' => Session::Get()->Username(),
                'minPrice' => $span['min'],
                'maxPrice' => $span['max'],
                'alert' => $alert
            ]);

        $response->setContent($html)->send(200);
    }

    function cart()
    {
        $response = new Response();

        $cart = Cart::Get();

        $schema = new Schema($_ENV['DB_NAME']);

        $keys = array_keys($_SESSION['cart']);
        $values = array_values($_SESSION['cart']);

        $in  = str_repeat('?,', count($keys) - 1) . '?';

        $stmt = $schema->prepare("SELECT p.*
            FROM ${_ENV['DB_PREFIX']}_products p
            WHERE p.id IN ($in)");
            
        $stmt->execute($keys);

        $products = $stmt->fetchAll(\PDO::FETCH_CLASS, Product::class);

        foreach ($products as &$product) {
            $quantity = array_pop($values)['quantity'];

            $product->{'requested'} = $quantity;

            if(!$product->image) {
                $product->image = 'images/no-image-scaled.png';
            }
        }

        $total = 0.0;

        for($i = 0; $i < sizeof($products); $i++) {
            $total += $products[$i]->getPrice();
        }

        $html = \GWM\Core\Template\Engine::Get()
        ->Parse("res/${_ENV['FALLBACK_THEME']}/src/store/cart.html.latte", [
            'total' => $total,
            'username' => Session::Get()->Username(),
            'products' => $products
        ]);

        $response->setContent($html)->send(200);
    }

    function Add(array $options = [])
    {
        $id = (int)$options['pux.route'][3]['vars']['id'] ?? 0;

        Cart::Get()->Add($id);

        Session::Get();

        $_SESSION['alert'] = [
            'message' => 'Product with id of '.$id.' has been added to cart.',
            'status' => 'ok'
        ];

        $response = new Response();
        $response->Redirect('/store');
    }

    function Manufacturers()
    {
        if(!Session::Get()->Logged()) {
            $response = new Response();
            $response->Astray();
        }

        $products = [];

        try {
            $schema = new Schema($_ENV['DB_NAME']);

            $model = new Product();
            $model->_INIT($schema);

            Schema::$PRIMARY_SCHEMA = $schema;

            $manufacturers = $schema->All(Manufacturer::class) ?? [];

            $latte = new \Latte\Engine;
            $latte->setTempDirectory('tmp/latte');
            $latte->render('themes/admin/templates/manufacturers.latte', array_merge(
                    \GWM\Core\Controllers\Dashboard::Defaults(), [
                    'username' => $_SESSION['username'] ?? '',
                    'avatar' => strtolower($_SESSION['username']) ?? '',
                    'firstname' => 'Firstname',
                    'lastname' => 'Lastname',
                    'ip' => Agent::ip(),
                    'manufacturers' => $manufacturers,
                ])
            );

        } catch (\ReflectionException $e) {
            Debug::$log[] = $e->getMessage();
        } catch (Basic $e) {
            Debug::$log[] = $e->getMessage();
        }

        exit;
    }

    function Products()
    {
        $response = new Response();

        if(!Session::Get()->Logged()) {
            $response->Astray();
        }

        $products = [];

        try {
            $schema = new Schema($_ENV['DB_NAME']);

            $model = new Product();
            $model->_INIT($schema);

            Schema::$PRIMARY_SCHEMA = $schema;

            $products = $schema->All(Product::class) ?? [];

            $product = $products[0];

        } catch (\ReflectionException $e) {
            Debug::$log[] = $e->getMessage();
        } catch (Basic $e) {
            Debug::$log[] = $e->getMessage();
        }

        $html = Engine::Get()->Parse('themes/admin/templates/products/index.latte',
            array_merge(\GWM\Core\Controllers\Dashboard::Defaults(), [
                'ip' => Agent::ip(),
                'products' => $products,
            ])
        );

        $response->setContent($html);
        $response->setStatus(200);
        $response->send();
        exit;
    }
}