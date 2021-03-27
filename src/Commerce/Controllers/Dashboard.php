<?php

namespace GWM\Commerce\Controllers {

    use GWM\Commerce\Models\ {
        Product,
        Category
    };

    use Latte\Engine;
    use GWM\Core\{Errors\Basic, Response, Schema, Session};
    use Mpdf\MpdfException;

    /**
     * Class Dashboard
     * @package GWM\Commerce
     * @version 1.0.0
     */
    class Dashboard
    {
        function Categories()
        {
            Session::Get()->Logged() or $response->Astray();

            try {
                $schema = new Schema($_ENV['DB_NAME']);

                $stmt = $schema->prepare("SELECT * FROM {$_ENV['DB_PREFIX']}_categories");
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $categories = $schema->All([
                        'categories',
                        Category::class
                    ]);
                }
            } 
            catch(\Exception $e)
            {
                die($e->getMessage());
            }

            $html = \GWM\Core\Template\Engine::Get()->Parse('themes/admin/templates/categories.latte', array_merge(
                \GWM\Core\Controllers\Dashboard::Defaults(), [
                    'categories' => $categories
                ]));

            $response = new Response;
            $response->setContent($html)->send();
        }

        function Affiliates()
        {
            Session::Get()->Logged() or $response->Astray();

            $html = \GWM\Core\Template\Engine::Get()->Parse('themes/admin/templates/affiliates.latte', array_merge(
                    \GWM\Core\Controllers\Dashboard::Defaults(),
                    [
                        
                    ]));

            $response = new Response;
            $response->setContent($html)->send();
        }

        function Shipping()
        {
            Session::Get()->Logged() or $response->Astray();

            $html = \GWM\Core\Template\Engine::Get()->Parse('themes/admin/templates/shipping.latte', array_merge(
                    \GWM\Core\Controllers\Dashboard::Defaults(),
                    [
                        
                    ]));

            $response = new Response;
            $response->setContent($html)->send();
        }

        function Orders()
        {
            Session::Get()->Logged() or $response->Astray();

            $schema = new Schema($_ENV['DB_NAME']);
            
            $orders = $schema->All(\GWM\Commerce\Models\Order::class);

            $html = \GWM\Core\Template\Engine::Get()->Parse('themes/admin/templates/commerce_orders.latte', array_merge(
                    \GWM\Core\Controllers\Dashboard::Defaults(),
                    [
                        'orders' => $orders
                    ]));

            $response = new Response;
            $response->setContent($html)->send();
        }

        function Discounts()
        {
            Session::Get()->Logged() or $response->Astray();

            $html = \GWM\Core\Template\Engine::Get()->Parse('themes/admin/templates/discounts.latte', array_merge(
                    \GWM\Core\Controllers\Dashboard::Defaults(),
                    [
                        
                    ]));

            $response = new Response;
            $response->setContent($html)->send();
        }

        function Currencies()
        {
            Session::Get()->Logged() or $response->Astray();

            $html = \GWM\Core\Template\Engine::Get()->Parse('themes/admin/templates/currencies.latte', array_merge(
                    \GWM\Core\Controllers\Dashboard::Defaults(),
                    [
                        
                    ]));

            $response = new Response;
            $response->setContent($html)->send();
        }

        function Edit(array $options = [])
        {
            Session::Get()->Logged() or $response->Astray();

            $response = new Response();

            if (!Session::Get()->Logged()) {
                $response->Astray();
            }

            try {
                $schema = new Schema($_ENV['DB_NAME']);
            } catch (Basic $e) {
                die($e->getMessage());
            }

            Schema::$PRIMARY_SCHEMA = $schema;

            if((int)isset($_POST['form']) > 0) {

                if($_POST['manufacturer'] <= 0) {
                    $_POST['manufacturer'] = null;
                }

                $data = [
                    'title' => $_POST['title'] ?? '',
                    'description' => $_POST['description'] ? htmlentities($_POST['description']) : '',
                    'manufacturer' => $_POST['manufacturer'] ?? null,
                    'id' => $_POST['form']
                ];

                if ($_FILES['imageUpload']['tmp_name'] != null) {
                    if (substr($_FILES['imageUpload']['type'], 0, 5) == 'image') {
                        $dest = GWM['DIR_PUBLIC'] . '/uploads/' . $_FILES["imageUpload"]["name"];

                        if (file_exists($dest)) {
                            unlink($dest);
                        }

                        if (move_uploaded_file($_FILES["imageUpload"]["tmp_name"], $dest)) {
                            $data['image'] = '/uploads/' . $_FILES['imageUpload']['name'];
                        }
                    }
                }

                if (isset($data['image'])) {
                    $statement = $schema->prepare("UPDATE {$_ENV['DB_PREFIX']}_products
                    SET title=:title, description=:description, manufacturer=:manufacturer, image=:image
                    WHERE id=:id
                ");
                } else {
                    $statement = $schema->prepare("UPDATE {$_ENV['DB_PREFIX']}_products
                    SET title=:title, description=:description, manufacturer=:manufacturer
                    WHERE id=:id
                ");
                }

                if ($statement->execute($data)) {

                    $response->Redirect('/dashboard/store/products', 301);
                } else {
                    die('Failed!');
                }
            }

            $id = (int)$options['pux.route'][3]['vars']['id'] ?? 0;

            $latte = new Engine;
            $latte->setTempDirectory('tmp/latte');

            $product = new Product();
            $product = $schema->Get(Product::class, $product, [
                'id' => $id
            ]);

            if($product instanceof Product) {
                $product->description = html_entity_decode($product->description, ENT_QUOTES);
            }

            $html = $latte->renderToString('themes/admin/templates/products/edit.latte',
                array_merge(\GWM\Core\Controllers\Dashboard::Defaults(),
                [
                    'username' => $_SESSION['username'] ?? '',
                    'avatar' => strtolower($_SESSION['username']) ?? '',
                    'product' => $product
                ])
            );

            $response = new Response;
            $response->setContent($html);
            $response->send();
            exit;
        }
    }
}