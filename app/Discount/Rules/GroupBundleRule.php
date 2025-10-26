<?php

namespace App\Discount\Rules;

use App\Contracts\DiscountRuleInterface;
use App\DTO\Cart\CartSnapshot;
use App\Models\UserProductGroup;

class GroupBundleRule implements DiscountRuleInterface
{
    public function __construct() {}

    public function compute(CartSnapshot $snapshot): float
    {
        $available = collect($snapshot->qtyMap());
        $prices    = collect($snapshot->priceMap());

        return UserProductGroup::query()
            ->with('items:group_id,product_id')
            ->get(['group_id', 'discount'])
            ->reduce(function (float $total, UserProductGroup $group) use (&$available, $prices) {
                $ids = $group->items->pluck('product_id')->map(fn($v) => (int) $v);

                if ($ids->isEmpty() || $ids->contains(fn($id) => ($available[$id] ?? 0) <= 0)) {
                    return $total;
                }

                // მინიმალური რაოდენობა
                $minSets = $ids->map(fn($id) => $available[$id])->min();
                if ($minSets <= 0) {
                    return $total;
                }

                //  ჯამური ფასი x1
                $oneSetSum = $ids->map(fn($id) => $prices[$id] ?? 0)->sum();

                // ფასდაკლება
                $discount = $minSets * $oneSetSum * ($group->discount / 100);
                $total += $discount;

                // გამოვაკლოთ უკვე გამოყენებული რაოდენობა
                $ids->each(fn($id) => $available[$id] -= $minSets);

                return $total;
            }, 0.0);
    }
}
