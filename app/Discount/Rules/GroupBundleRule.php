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
        $availableQty = $snapshot->qtyMap();
        $priceMap     = $snapshot->priceMap();

        $groups = UserProductGroup::query()
            ->with(['items:group_id,product_id'])
            ->get(['group_id','user_id','discount']);

        if ($groups->isEmpty()) return 0.0;

        $total = 0.0;

        foreach ($groups as $g) {
            $productIds = $g->items->pluck('product_id')->map(fn($v)=>(int)$v)->all();
            if (empty($productIds)) continue;

            // ყველა პროდუქტი უნდა იყოს ხელმისაწვდომი
            $hasAll = true;
            foreach ($productIds as $pid) {
                if (($availableQty[$pid] ?? 0) <= 0) { $hasAll = false; break; }
            }
            if (!$hasAll) continue;

            // მინიმალური პროდუქტის რაოდენობა (თუ პროდუქტებს შორის რაოდენობრივი სხვაობაა ავიღოთ მინიმალური)
            $minSets = PHP_INT_MAX;
            foreach ($productIds as $pid) $minSets = min($minSets, (int)$availableQty[$pid]);
            if ($minSets <= 0) continue;

            // ერთი სეტის ჯამი
            $oneSetSum = 0.0;
            foreach ($productIds as $pid) $oneSetSum += (float)($priceMap[$pid] ?? 0.0);

            $groupDiscount = $minSets * $oneSetSum * ((float)$g->discount / 100.0);
            $total += $groupDiscount;

            foreach ($productIds as $pid) $availableQty[$pid] -= $minSets;

            $productIds->each(fn ($pid) => $availableQty[$pid] -= $minSets);
        }

        return $total;
    }
}
