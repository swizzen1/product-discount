<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\UserProductGroup;
use App\Models\ProductGroupItem;
use App\Models\CartItem;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['product_id' => 1, 'user_id' => 10, 'title' => 'produqti 1', 'price' => 10],
            ['product_id' => 2, 'user_id' => 10, 'title' => 'produqti 2', 'price' => 15],
            ['product_id' => 3, 'user_id' => 10, 'title' => 'produqti 3', 'price' => 8],
            ['product_id' => 4, 'user_id' => 10, 'title' => 'produqti 4', 'price' => 7],
            ['product_id' => 5, 'user_id' => 10, 'title' => 'produqti 5', 'price' => 20],
        ];
        foreach ($products as $p) Product::query()->updateOrCreate(['product_id' => $p['product_id']], $p);

        $group = UserProductGroup::query()->updateOrCreate(
            ['group_id' => 1],
            ['user_id' => 10, 'discount' => 15]
        );
        ProductGroupItem::query()->updateOrCreate(['group_id' => $group->group_id, 'product_id' => 2], []);
        ProductGroupItem::query()->updateOrCreate(['group_id' => $group->group_id, 'product_id' => 5], []);

        CartItem::query()->updateOrCreate(['user_id' => 15, 'product_id' => 2], ['quantity' => 3]);
        CartItem::query()->updateOrCreate(['user_id' => 15, 'product_id' => 5], ['quantity' => 2]);
        CartItem::query()->updateOrCreate(['user_id' => 15, 'product_id' => 1], ['quantity' => 1]);
    }
}
