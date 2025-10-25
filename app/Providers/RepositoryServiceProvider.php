<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\CartRepositoryInterface;
use App\Repositories\CartRepository;
use App\Services\DiscountEngine;
use App\Discount\Rules\GroupBundleRule;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CartRepositoryInterface::class, CartRepository::class);

        $this->app->singleton(DiscountEngine::class, function ($app) {
            $engine = new DiscountEngine;

            // თუ გვაქვს რამოდენიმე ლოგიკა ფასდაკლებაზე შეგვიძლია გადავცეთ ყველა ფასდაკლების ობიექტი
            $engine->addRule(new GroupBundleRule());

            return $engine;

        });
    }
}
