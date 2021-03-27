<?php

namespace GWM\Commerce\Models;

use GWM\Core\Model;
use GWM\Core\Schema;

/**
 * Class Product
 *
 * No description.
 *
 * @package GWM\Commerce\Models
 * @property-read Manufacturer $manufacturer
 * @version 1.0.0
 */
class Product extends Model
{
    /**
     * Undocumented variable
     *
     * @var int (primary)
     */
    public int $id;

    /**
     * A product title should succinctly
     * explain what product is all about.
     * Product titles should never be filled
     * with keywords, rebate offers, claims,
     * sales messaging, or anything else other
     * than the product and its distinguishing
     * features.
     *
     * @var string(255)
     */
    public string $title;

    /**
     *
     *
     * @var string|null(text)
     */
    public ?string $description = null;

    /**
     *
     *
     * @var string|null(255)
     */
    public ?string $image = null;

    /**
     *
     *
     * @var int|null
     */
    protected ?int $manufacturer = null;

    /**
     *
     *
     * @var int
     */
    public int $stock;

    /**
     *
     *
     * @var string(decimal="10.2")
     */
    public string $price;

    /**
     *
     *
     * @var bool
     */
    public bool $status;

    /**
     * @magic
     */
    function __construct()
    {
        if ($this->price ?? false) {
            $this->price .= "\xE2\x82\xAc";
        }
    }

    public function __get($propertyName): ?Manufacturer
    {
        if ($propertyName == 'manufacturer') {
            $manufacturer = new Manufacturer();

            if($this->manufacturer ?? false) {
                $manufacturer = Schema::$PRIMARY_SCHEMA->Get(Manufacturer::class, $manufacturer, [
                    'id' => $this->manufacturer
                ]);
            }

            return $manufacturer;
        }

        return null;
    }

    public function getPrice()
    {
        return (double)rtrim($this->price, '€');
    }

    function _INIT($schema)
    {
        $this->schema = $schema;
        $this->table = 'products';

        $schema->Create(Product::class, $this->table);
    }
}