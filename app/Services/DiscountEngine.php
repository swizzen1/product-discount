<?php

namespace App\Services;

use App\Contracts\DiscountRuleInterface;
use App\DTO\Cart\CartSnapshot;

class DiscountEngine
{
    public function __construct(private array $rules = [])
    {
        $this->rules = $rules;
    }

    public function addRule(DiscountRuleInterface $rule): void
    {
        $this->rules[] = $rule;
    }

    public function computeTotal(CartSnapshot $snapshot): float
    {
        $sum = 0.0;
        foreach ($this->rules as $rule) {
            $sum += $rule->compute($snapshot);
        }
        return round($sum, 2);
    }
}
