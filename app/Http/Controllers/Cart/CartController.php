<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddRequest;
use App\Http\Requests\Cart\RemoveRequest;
use App\Http\Requests\Cart\SetQtyRequest;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    private int $userId;

    public function __construct(private CartService $service)
    {
        $this->userId = 15;
    }

    public function add(AddRequest $request): JsonResponse
    {
        $this->service->add($this->userId, (int)$request->product_id, (int)($request->quantity ?? 1));

        return response()->json(['message'=>'Added to cart']);
    }

    public function remove(RemoveRequest $request): JsonResponse
    {
        $this->service->remove($this->userId, (int)$request->product_id);

        return response()->json(['message'=>'Removed from cart']);
    }

    public function setQuantity(SetQtyRequest $request): JsonResponse
    {
        $this->service->setQuantity($this->userId, (int)$request->product_id, (int)$request->quantity);

        return response()->json(['message'=>'Quantity updated']);
    }

    public function show(): JsonResponse
    {
        return response()->json($this->service->snapshot($this->userId));
    }
}
