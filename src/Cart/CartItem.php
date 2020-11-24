<?php

namespace App\Cart;

class CartItem
{
    public $product;
    public $count;

    public function __construct($product, $count)
    {
        $this->product = $product;
        $this->count = $count;
    }

    public function getTotal()
    {
        return $this->product->getPrice() * $this->count;
    }
}
