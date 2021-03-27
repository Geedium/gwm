<?php

namespace GWM\Features\Commerce\Controllers {

    use GWM\Commerce\Models\Product;

    use GWM\Core\{Schema, Session, Response, Errors\Basic, Template\Engine};

    /**
     * Class Dashboard
     * @package GWM\Features\Commerce\Controllers
     * @version 1.0.0
     */
    class Dashboard
    {
        /**
         * @throws \Exception
         * @since 1.0.0
         */
        function Create()
        {
            $response = new Response();

            if (!Session::Get()->Logged()) {
                $response->Astray();
            }

            if($_POST['submit'] != null)
            {
                $title = filter_input(INPUT_POST, 'title');
                $price = filter_input(INPUT_POST, 'price');
                $status = filter_input(INPUT_POST, 'status');

                if($status == null)
                {
                    $status = 0;
                }

                try {
                    $schema = new Schema($_ENV['DB_NAME']);
                    Schema::$PRIMARY_SCHEMA = $schema;

                    $st = $schema->prepare("INSERT INTO {$_ENV['DB_PREFIX']}_products
                        (title, price, status) VALUES (:title, :price, :status)
                    ");

                    if($st->execute([
                        'title' => $title,
                        'price' => $price,
                        'status' => $status == 'on'
                    ])) {
                        $response->Redirect('/dashboard/store/products');
                    } else {
                        var_dump($title, $price, $status);
                        die('Failed!');
                    }
                    
                } catch(Basic $e) {
                    die($e->getMessage());
                }
            }

            $html = Engine::Get()->Parse('themes/admin/templates/products/create.latte',
                array_merge(\GWM\Core\Controllers\Dashboard::Defaults(), [

                ])
            );

            $response->setContent($html)->send();
        }

        /**
         * @param array $options
         * @since 1.0.0
         */
        function Delete(array $options = [])
        {
            $response = new Response();
            $id = (int)$options['pux.route'][3]['vars']['id'] ?? 0;

            if (!Session::Get()->Logged()) {
                $response->Astray();
            }

            try {
                $schema = new Schema($_ENV['DB_NAME']);
                Schema::$PRIMARY_SCHEMA = $schema;

                $product = new Product();

                Schema::$PRIMARY_SCHEMA->Get(Product::class, $product, [
                    'id' => $id
                ], function($product) use ($response) {

                    $st = Schema::$PRIMARY_SCHEMA->prepare("DELETE FROM {$_ENV['DB_PREFIX']}_products WHERE id = :id");
                    if($st->execute([
                        ':id' => $product->id
                    ])) {
                        $response->Redirect('/dashboard/store/products', 301);
                    } else {
                        $response->Astray();
                    }
                });

            } catch (Basic $e) {
                die($e->getMessage());
            }
        }
    }
}