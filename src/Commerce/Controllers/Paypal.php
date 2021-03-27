<?php

namespace GWM\Commerce\Controllers {

    use GWM\Core\Response;
    use GWM\Core\Schema;
    use GWM\Core\Template\Engine;

    use GWM\Commerce\Models\Order;

    use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
    use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
    use PayPalCheckoutSdk\Core\PayPalHttpClient;
    use PayPalCheckoutSdk\Core\SandboxEnvironment;

    /**
     * Class Paypal
     * @package GWM\Commerce\Controllers
     */
    class Paypal
    {
        public function accept(array $options)
        {
            $clientId = $_ENV['PAYPAL_SANDBOX_CLIENT_ID'];
            $clientSecret = $_ENV['PAYPAL_SANDBOX_CLIENT_SECRET'];

            $environment = new SandboxEnvironment($clientId, $clientSecret);
            $client = new PayPalHttpClient($environment);

            $query = $options['pux.route'][3]['vars']['query'];
            parse_str($query, $vars);

            $request = new OrdersCaptureRequest($vars['token']);
            $request->prefer('return=representation');
            try {
                $response = $client->execute($request);

                $res = new Response();

                $schema = new Schema($_ENV['DB_NAME']);
                
                $model = new Order();
                $model->_INIT($schema);

                $model->txn_id = $response->result->id;

                $stmt = $schema->prepare("INSERT INTO {$_ENV['DB_PREFIX']}_orders (txn_id) VALUES (?) ");
                $stmt->execute([
                    $model->txn_id
                ]);

                $html = \GWM\Core\Template\Engine::Get()
                    ->Parse('resources/{b186213c-38ba-47ad-b2e4-f235acabd0d0}/templates/details.latte', [
                        'status' => $response->result->status,
                        'txnid' => $model->txn_id,
                        'firstname' => $response->result->payer->name->given_name,
                        'lastname' => $response->result->payer->name->surname
                    ]);

                $res->setContent($html)->send(200);
            } catch (HttpException $ex) {
                echo $ex->statusCode;
                print_r($ex->getMessage());
            }
        }

        public function index()
        {
            $clientId = $_ENV['PAYPAL_SANDBOX_CLIENT_ID'];
            $clientSecret = $_ENV['PAYPAL_SANDBOX_CLIENT_SECRET'];

            $environment = new SandboxEnvironment($clientId, $clientSecret);
            $client = new PayPalHttpClient($environment);

            $request = new OrdersCreateRequest();
            $request->prefer('return=representation');
            $request->body = [
                                 "intent" => "CAPTURE",
                                 "purchase_units" => [[
                                     "reference_id" => "test_ref_id1",
                                     "amount" => [
                                         "value" => "100.00",
                                         "currency_code" => "USD"
                                     ]
                                 ]],
                                 "application_context" => [
                                      "cancel_url" => "http://example.com/cancel",
                                      "return_url" => $_ENV['PAYPAL_RETURN_URL']
                                 ]
                             ];
            
            try {
                // Call API with your client and get a response for your call
                $response = $client->execute($request);
                
                // If call returns body in response, you can get the deserialized version from the result attribute of the response
                // echo '<pre>';
                //var_dump($response->result->links);
                //echo '</pre>';

                $res = new Response();

                $html = \GWM\Core\Template\Engine::Get()
                ->Parse('resources/{b186213c-38ba-47ad-b2e4-f235acabd0d0}/templates/checkout.latte', [
                    'paypal_url' => $response->result->links[1]->href
                ]);

                $res->setContent($html)->send(200);
            } catch (HttpException $ex) {
                echo $ex->statusCode;
                print_r($ex->getMessage());
            }
        }
    }
}