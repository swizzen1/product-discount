<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cart\CartController;

Route::get('/', function () {});

Route::post('/addProductInCart', [CartController::class, 'add']);
Route::post('/removeProductFromCart', [CartController::class, 'remove']);
Route::post('/setCartProductQuantity', [CartController::class, 'setQuantity']);
Route::get('/getUserCart', [CartController::class, 'show']);
