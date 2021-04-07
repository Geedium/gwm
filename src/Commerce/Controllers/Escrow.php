<?php

/**
 * Escrow transaction interface
 */
interface Id92db41814
{
    public function Create(&$_OUT, array $data);
}

namespace GWM\Commerce\Controllers {
    use GWM\Core\Response;
    use GWM\Commerce\Base\Escrow\Transaction;

    class Escrow
    {
        function __construct()
        {
            
        }

        public function retrieve_transaction($id)
        {
            $json = '{}';

            Transaction::Get()
                ->Auth($_ENV['ESCROW_USER'], $_ENV['ESCROW_PASS'])
                ->Retrieve($json, $id)
                ->Dispose();

            return json_decode($json);
        }

        /**
         * Creates new transaction for customer.
         * @since 1.1.0
         * @return mixed Json.
         */
        public function create_transaction()
        {
            $json = '{}';

            Transaction::Get()
                ->Auth($_ENV['ESCROW_USER'], $_ENV['ESCROW_PASS'])
                ->Create($json, [
                    'currency' => 'eur',
                    'items' => array(
                        array(
                            'description' => 'No description.',
                            'schedule' => array(
                                array(
                                    'payer_customer' => 'keanu.reaves@test.escrow.com',
                                    'amount' => '1000.0',
                                    'beneficiary_customer' => 'me',
                                ),
                            ),
                            'title' => 'domain.tld',
                            'inspection_period' => '259200',
                            'type' => 'domain_name',
                            'quantity' => '1',
                            'extra_attributes' => array(
                                'image_url' => 'https://i.ebayimg.com/images/g/RicAAOSwzO5e3DZs/s-l1600.jpg',
                                'merchant_url' => 'https://www.ebay.com'
                            ),
                        ),
                    ),
                    'description' => 'No description.',
                    'parties' => array(
                        array(
                            'customer' => 'me',
                            'role' => 'seller',
                        ),
                        array(
                            'customer' => 'keanu.reaves@test.escrow.com',
                            'role' => 'buyer',
                        ),
                    ),
                ])->Dispose();

            return json_decode($json);
        }

        public function testTransaction()
        {
            $response = new Response();

            $json = '{}';

            Transaction::Get()
                ->Auth($_ENV['ESCROW_USER'], $_ENV['ESCROW_PASS'])
                ->Create($json, [
                    'currency' => 'usd',
                    'items' => array(
                        array(
                            'description' => 'johnwick.com',
                            'schedule' => array(
                                array(
                                    'payer_customer' => 'keanu.reaves@test.escrow.com',
                                    'amount' => '1000.0',
                                    'beneficiary_customer' => 'me',
                                ),
                            ),
                            'title' => 'johnwick.com',
                            'inspection_period' => '259200',
                            'type' => 'domain_name',
                            'quantity' => '1',
                            'extra_attributes' => array(
                                'image_url' => 'https://i.ebayimg.com/images/g/RicAAOSwzO5e3DZs/s-l1600.jpg',
                                'merchant_url' => 'https://www.ebay.com'
                            ),
                        ),
                    ),
                    'description' => 'The sale of johnwick.com',
                    'parties' => array(
                        array(
                            'customer' => 'me',
                            'role' => 'seller',
                        ),
                        array(
                            'customer' => 'keanu.reaves@test.escrow.com',
                            'role' => 'buyer',
                        ),
                    ),
                ])->Dispose();

            $response->setHeaders([
                'Content-Type: application/json'
            ]);

            $response->setContent($json)->send(201);
        }
    }
}