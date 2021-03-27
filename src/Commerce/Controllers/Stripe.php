<?php

namespace GWM\Commerce\Controllers {
    use Stripe\Stripe as Library;

    class Stripe
    {
        function Checkout()
        {
            Library::setApiKey($_ENV['STRIPE_SECRET_KEY']);

            $response = ['status' => 0, 'error' => [
                'message' => 'Invalid Request!'
            ],];

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $input = file_get_contents('php://input');
                $request = json_decode($input);
            }

            if (json_last_error() != JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode($response);
                exit;
            }

            if (!empty($request->checkoutSession)) {
                try {
                    $session = \Stripe\Checkout\Session::create([
                        'payment_method_types' => ['card'],
                        'line_items' => [[
                            'price_data' => [
                                'product_data' => [
                                    'name' => 'ITEM ID',
                                    'metadata' => [
                                        'pro_id' => '1'
                                    ]
                                ],
                                'unit_amount' => '32',
                                'currency' => 'EURO',
                            ],
                            'quantity' => 1,
                            'description' => 'No description.',
                        ]],
                        'mode' => 'payment',
                        'success_url' => STRIPE_SUCCESS_URL . '?session_id={CHECKOUT_SESSION_ID}',
                        'cancel_url' => STRIPE_CANCEL_URL,
                    ]);
                } catch (\Exception $e) {
                    $api_error = $e->getMessage();
                }

                if (empty($api_error) && $session) {
                    $response = array(
                        'status' => 1,
                        'message' => 'Checkout Session created successfully!',
                        'sessionId' => $session['id']
                    );
                } else {
                    $response = array(
                        'status' => 0,
                        'error' => array(
                            'message' => 'Checkout Session creation failed! ' . $api_error
                        )
                    );
                }
            }

            // Return response
            echo json_encode($response);
        }
    }
}