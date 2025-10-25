<?php

namespace App\Repositories;

use App\Contracts\CartRepositoryInterface;
use App\DTO\Cart\CartLine;
use App\Models\CartItem;

class CartRepository implements CartRepositoryInterface
{
    public function add(int $userId, int $productId, int $qty = 1): void
    {
        $row = CartItem::query()->where(['user_id' => $userId, 'product_id' => $productId])->first();

        if ($row) {
            $row->update(['quantity' => max(1, $row->quantity + $qty)]);
        } else {
            CartItem::create(['user_id' => $userId, 'product_id' => $productId, 'quantity' => $qty]);
        }
    }

    public function remove(int $userId, int $productId): void
    {
        CartItem::query()->where(['user_id' => $userId, 'product_id' => $productId])->delete();
    }

    public function setQuantity(int $userId, int $productId, int $qty): void
    {
        if ($qty <= 0) {
            $this->remove($userId, $productId);
            return;
        }

        CartItem::query()->updateOrCreate(
            ['user_id' => $userId, 'product_id' => $productId],
            ['quantity' => $qty]
        );
    }

    public function getCartItems(int $userId): array
    {
        $cartProducts = CartItem::query()
            ->with('product:product_id,price')
            ->forUser($userId)
            ->get(['product_id', 'quantity']);

        return $cartProducts->map(fn(CartItem $r) => new CartLine(
            (int)$r->product_id,
            (int)$r->quantity,
            (float)$r->product->price
        ))->all();
    }

    public function getQtyMap(int $userId): array
    {
        return CartItem::query()
            ->forUser($userId)
            ->pluck('quantity', 'product_id')
            ->map(fn($q) => (int)$q)
            ->toArray();
    }
}
