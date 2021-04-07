<?php

namespace GWM\Commerce\Models {

    use GWM\Core\Model;
    use GWM\Commerce\Models\Customer;

    /**
     * Class Order
     * @package GWM\Commerce\Models
     * @version 1.0.0
     */
    class Order extends Model
    {
        /**
         * Undocumented variable
         *
         * @var int (primary)
         */
        public int $id;

        /**
         * Undocumented variable
         *
         * @var int|null
         */
        protected ?int $customer_id = null;

        /**
         * Transaction ID
         *
         * @var string (text)
         */
        public string $txn_id;

        /**
         * Payment method.
         * 
         * @var string (255)
         */
        public ?string $method = 'default';

        /**
         * Completed
         * 
         * @var bool
         */
        public bool $completed;
        
        function __construct() {

            if ($this->method == 'escrow' && $this->id > 0) {
                $escrow = new \GWM\Commerce\Controllers\Escrow();
                $data = $escrow->retrieve_transaction($this->txn_id);
                $agreed = true;

                foreach ($data->parties as $party) {
                    $agreed &= $party->agreed;
                }

                $total = 0;

                $subtract = $data->items[0]->amount - $data->items[0]->amount_without_taxes;
                $fees = $subtract * 100 / 100;

                foreach ($data->items as $item) {
                    $total += $item->amount;
                    $fees += ($item->amount - $amount_without_taxes);
                }

                $this->{'fees'} = $fees;
                $this->{'total'} = $total;
                $this->{'customer'} = $data->parties[0]->customer;
                $this->{'created_at'} = $data->creation_date;
                $this->{'shop'} = 0;
                $this->{'status'} = 'Waiting for confirmation...';
            }
            
        }

        public function __get($propertyName): ?Customer
        {
            if ($propertyName == 'customer_id') {
                $customer = new Customer();
    
                if ($this->customer_id ?? false) {
                    $customer = Schema::$PRIMARY_SCHEMA->Get(Customer::class, $customer, [
                        'id' => $this->customer_id
                    ]);
                }
    
                return $customer;
            }
    
            return null;
        }

        function _INIT($schema)
        {
            $this->schema = $schema;
            $this->table = 'orders';

            $schema->Create(Order::class, $this->table);
        }
    }
}