<?php

namespace App\Services;

use App\DTO\Cart\CartLine;
use App\DTO\Cart\CartSnapshot;
use App\Services\DiscountEngine;
use App\Contracts\CartRepositoryInterface;

class CartService
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
        private DiscountEngine $discounts
    ) {}

    public function add(int $userId, int $productId, int $qty = 1): void
    {
        $this->cartRepository->add($userId, $productId, $qty);
    }

    public function remove(int $userId, int $productId): void
    {
        $this->cartRepository->remove($userId, $productId);
    }

    public function setQuantity(int $userId, int $productId, int $qty = 1): void
    {
        $this->cartRepository->setQuantity($userId, $productId, $qty);
    }

    /**
     * აბრუნებს პროდუქტებს და ფასდაკლებას
     */
    public function snapshot(int $userId): array
    {
        $cartItems = $this->cartRepository->getCartItems($userId);

        $snapshot = new CartSnapshot($cartItems);

        $discount = $this->discounts->computeTotal($snapshot);

        $products = array_map(fn(CartLine $l) => [
            'product_id' => $l->productId,
            'quantity'  => $l->quantity,
            'price'     => $l->unitPrice,
        ], $cartItems);

        return ['products' => $products, 'discount' => $discount];
    }
}
