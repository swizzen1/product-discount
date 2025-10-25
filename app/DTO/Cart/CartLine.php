<?php

namespace App\DTO\Cart;

class CartLine
{
    public function __construct(
        public int $productId,
        public int $quantity,
        public float $unitPrice
    ) {}
}
