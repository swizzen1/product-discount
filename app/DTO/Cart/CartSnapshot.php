<?php

namespace App\DTO\Cart;

class CartSnapshot
{
    public function __construct(public array $cartItems) {}

    public function qtyMap(): array
    {
        $map = [];
        foreach ($this->cartItems as $l) $map[$l->productId] = $l->quantity;
        return $map;
    }

    public function priceMap(): array
    {
        $map = [];
        foreach ($this->cartItems as $l) $map[$l->productId] = $l->unitPrice;
        return $map;
    }
}
