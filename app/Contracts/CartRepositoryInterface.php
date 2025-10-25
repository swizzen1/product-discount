<?php

namespace App\Contracts;

interface CartRepositoryInterface
{
    public function add(int $userId, int $productId, int $qty = 1): void;
    public function remove(int $userId, int $productId): void;
    public function setQuantity(int $userId, int $productId, int $qty): void;

    public function getCartItems(int $userId): array;

    public function getQtyMap(int $userId): array;
}
