<?php

namespace GWM\Commerce\Models;

class Product
{
    function __construct($schema)
    {
        $schema->Create(Product::class, 'products');
    }

    public function Select($schema, $min, $max)
    {
        $pdo = $schema->prepare("SELECT * WHERE `price` BETWEEN $min AND $max");
        $pdo->execute();

        return $pdo->fetchAll(\PDO::FETCH_ASSOC);
    }
}