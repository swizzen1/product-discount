<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Product;
use App\Models\UserProductGroup;
use App\Models\ProductGroupItem;
use App\Models\CartItem;
use App\Services\CartService;
use App\Services\DiscountEngine;
use App\Discount\Rules\GroupBundleRule;
use App\Contracts\CartRepositoryInterface;
use App\Repositories\CartRepository;

class DiscountEngineTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // მოდულების binding–ები (DIP)
        $this->app->bind(CartRepositoryInterface::class, CartRepository::class);

        // DiscountEngine singleton + Rule
        $this->app->singleton(DiscountEngine::class, function () {
            $engine = new DiscountEngine();
            // თუ გინდა merchant-ზე სკოპი, ჩასვი ownerUserId=10
            $engine->addRule(new GroupBundleRule());
            return $engine;
        });

        // აუცილებელია მიგრაციები ამოვარდეს
        $this->artisan('migrate', ['--force' => true]);
    }

    protected function seedScenario(): void
    {
        // Users (FK-ებისთვის)
        User::query()->updateOrCreate(
            ['id'=>10], ['name'=>'Merchant 10','email'=>'m10@example.com','password'=>Hash::make('pass')]
        );
        User::query()->updateOrCreate(
            ['id'=>15], ['name'=>'Buyer 15','email'=>'b15@example.com','password'=>Hash::make('pass')]
        );

        // Products
        Product::query()->updateOrCreate(['product_id'=>1], ['user_id'=>10,'title'=>'p1','price'=>10]);
        Product::query()->updateOrCreate(['product_id'=>2], ['user_id'=>10,'title'=>'p2','price'=>15]);
        Product::query()->updateOrCreate(['product_id'=>3], ['user_id'=>10,'title'=>'p3','price'=>8]);
        Product::query()->updateOrCreate(['product_id'=>4], ['user_id'=>10,'title'=>'p4','price'=>7]);
        Product::query()->updateOrCreate(['product_id'=>5], ['user_id'=>10,'title'=>'p5','price'=>20]);

        // Group {2,5} with 15%
        $group = UserProductGroup::query()->updateOrCreate(['group_id'=>1], ['user_id'=>10,'discount'=>15]);
        ProductGroupItem::query()->updateOrCreate(['group_id'=>$group->group_id,'product_id'=>2], []);
        ProductGroupItem::query()->updateOrCreate(['group_id'=>$group->group_id,'product_id'=>5], []);
    }

    public function test_discount_matches_example_10_5()
    {
        $this->seedScenario();

        // Cart: #2 x3, #5 x2, #1 x1
        CartItem::query()->updateOrCreate(['user_id'=>15,'product_id'=>2], ['quantity'=>3]);
        CartItem::query()->updateOrCreate(['user_id'=>15,'product_id'=>5], ['quantity'=>2]);
        CartItem::query()->updateOrCreate(['user_id'=>15,'product_id'=>1], ['quantity'=>1]);

        /** @var CartService $service */
        $service = $this->app->make(CartService::class);

        $result = $service->snapshot(15);

        $this->assertEquals(10.5, $result['discount']);
        $this->assertCount(3, $result['products']);

        // პროდუქტის ფასები სწორად ბრუნდება
        $indexed = collect($result['products'])->keyBy('product_id');
        $this->assertSame(15.0, $indexed[2]['price']);
        $this->assertSame(20.0, $indexed[5]['price']);
        $this->assertSame(10.0, $indexed[1]['price']);
    }

    public function test_no_discount_if_one_group_item_missing()
    {
        $this->seedScenario();

        // Cart: მარტო #2 x3 (არ არის #5) -> ფასდაკლება არაა
        CartItem::query()->updateOrCreate(['user_id'=>15,'product_id'=>2], ['quantity'=>3]);

        $service = $this->app->make(CartService::class);
        $result = $service->snapshot(15);

        $this->assertEquals(0.0, $result['discount']);
    }

    public function test_discount_uses_minimal_sets()
    {
        $this->seedScenario();

        // Cart: #2 x10, #5 x1
        CartItem::query()->updateOrCreate(['user_id'=>15,'product_id'=>2], ['quantity'=>10]);
        CartItem::query()->updateOrCreate(['user_id'=>15,'product_id'=>5], ['quantity'=>1]);

        $service = $this->app->make(CartService::class);
        $result = $service->snapshot(15);

        // (15 + 20) * 15% = 5.25
        $this->assertEquals(5.25, $result['discount']);
    }

    public function test_multiple_groups_do_not_double_count_same_units()
    {
        $this->seedScenario();

        // დავამატოთ მეორე ჯგუფი {1,2} 10%
        $g2 = UserProductGroup::query()->create(['user_id'=>10,'discount'=>10]);
        ProductGroupItem::query()->create(['group_id'=>$g2->group_id,'product_id'=>1]);
        ProductGroupItem::query()->create(['group_id'=>$g2->group_id,'product_id'=>2]);

        // Cart: #1 x1, #2 x3, #5 x2
        CartItem::query()->updateOrCreate(['user_id'=>15,'product_id'=>1], ['quantity'=>1]);
        CartItem::query()->updateOrCreate(['user_id'=>15,'product_id'=>2], ['quantity'=>3]);
        CartItem::query()->updateOrCreate(['user_id'=>15,'product_id'=>5], ['quantity'=>2]);

        $service = $this->app->make(CartService::class);
        $result = $service->snapshot(15);

        /**
         * წესით პირველად ჯგუფი {2,5} გამოიყენებს მინ.2 ერთეულს (#2 და #5), მისცემს 10.5-ს.
         * დარჩება #2 => 1 ცალი, #1 => 1 ცალი => ჯგუფი {1,2} მიიღებს მინ.1 სეტს:
         * (10 + 15) * 10% = 2.5
         * ჯამი: 10.5 + 2.5 = 13.0
         */
        $this->assertEquals(13.0, $result['discount']);
    }
}
