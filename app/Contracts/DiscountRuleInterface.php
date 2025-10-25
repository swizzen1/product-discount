<?php

namespace App\Contracts;

use App\DTO\Cart\CartSnapshot;

interface DiscountRuleInterface
{
    /** ფასდაკლება ერთეულებში  */
    public function compute(CartSnapshot $snapshot): float;
}
