<?php

namespace GWM\Commerce\Models {

    use GWM\Core\Model;

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
         * Transaction ID
         *
         * @var string (text)
         */
        public string $txn_id;
        
        function _INIT($schema)
        {
            $this->schema = $schema;
            $this->table = 'orders';

            $schema->Create(Order::class, $this->table);
        }
    }
}