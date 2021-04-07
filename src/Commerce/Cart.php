<?php

namespace GWM\Commerce {

    use GWM\Core\Session;
    use GWM\Core\Singleton;

    /**
     * Class Cart
     * @package GWM\Commerce
     * @version 1.0.0
     */
    class Cart extends Singleton
    {
        public float $total = 0.0;
        public string $currency = 'EUR';

        protected function Init(): void
        {
            Session::Get();

            if (!isset($_SESSION['cart'])):
                $_SESSION['cart'] = [];
            endif;
        }

        function Add(int $id, int $quantity = 1): self
        {
            if (\is_array($_SESSION['cart'])) {
                if (array_key_exists($id, $_SESSION['cart'])) {
                    $itemQuantity = $_SESSION['cart'][$id]['quantity'];
                    $itemQuantity += $quantity;
                    $_SESSION['cart'][$id] = [
                        'quantity' => $itemQuantity
                    ];
                } else {
                    $_SESSION['cart'][$id] = [
                        'quantity' => $quantity
                    ];
                }
            }

            return $this;
        }

        function Total(): int
        {
            if (\is_array($_SESSION['cart'])) {
                $total = 0;
                foreach ($_SESSION['cart'] as $key => $value) {
                    $total += $value['quantity'];
                }
                return $total;
            }
            return 0;
        }
    }
}