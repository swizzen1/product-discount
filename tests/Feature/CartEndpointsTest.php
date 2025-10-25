<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\ProductGroupItem;
use App\Models\UserProductGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

class CartWebEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate', ['--force' => true]);

        // CSRF TOKEN-ის შემოწმება არ გვჭირდება
        $this->withoutMiddleware(VerifyCsrfToken::class);

        // მომხმარებლების შექმნა
        User::query()->updateOrCreate(['id' => 10], [
            'name' => 'Merchant',
            'email' => 'm@example.com',
            'password' => bcrypt('x')
        ]);
        User::query()->updateOrCreate(['id' => 15], [
            'name' => 'Buyer',
            'email' => 'b@example.com',
            'password' => bcrypt('x')
        ]);

        // პროდუქტების შექმნა
        Product::query()->updateOrCreate(['product_id' => 1], ['user_id' => 10, 'title' => 'p1', 'price' => 10]);
        Product::query()->updateOrCreate(['product_id' => 2], ['user_id' => 10, 'title' => 'p2', 'price' => 15]);
        Product::query()->updateOrCreate(['product_id' => 5], ['user_id' => 10, 'title' => 'p5', 'price' => 20]);

        $group = UserProductGroup::query()->create(['user_id' => 10, 'discount' => 15]);
        ProductGroupItem::query()->create(['group_id' => $group->group_id, 'product_id' => 2]);
        ProductGroupItem::query()->create(['group_id' => $group->group_id, 'product_id' => 5]);
    }

    public function test_add_set_remove_and_get_cart_web_routes()
    {
        /**
         * პროდუქტი - #10 (მაგალითი)
         * რაოდენობა x3 (მაგალითი)
         */

        // /addProductInCart კალათაში დამატება - #2 x3
        $this->post('/addProductInCart', [
            'product_id' => 2,
            'quantity' => 3
        ])->assertStatus(200);

        // #5 x2
        $this->post('/addProductInCart', [
            'product_id' => 5,
            'quantity' => 2
        ])->assertStatus(200);

        // #2 => 4
        $this->post('/setCartProductQuantity', [
            'product_id' => 2,
            'quantity' => 4
        ])->assertStatus(200);

        // getUserCart უნდა დააბრუნოს discount = 10.5
        $this->get('/getUserCart?user_id=15')
            ->assertStatus(200)
            ->assertJsonStructure(['products', 'discount'])
            ->assertJson(['discount' => 10.5]);

        // უნდა წაიშალოს #5 და ფასდაკლება უნდა გახდეს 0
        $this->post('/removeProductFromCart', [
            'product_id' => 5
        ])->assertStatus(200);

        $this->get('/getUserCart?user_id=15')
            ->assertStatus(200)
            ->assertJson(['discount' => 0.0]);
    }

    // რაოდენობის შეცვლა 0 რაოდენობის შემთხვევაში წაიღოს კალათიდან პროდუქტი
    public function test_set_quantity_zero_removes_item_web_routes()
    {
        CartItem::query()->create(['user_id' => 15, 'product_id' => 2, 'quantity' => 2]);

        $this->post('/setCartProductQuantity', [
            'product_id' => 2,
            'quantity' => 0
        ])->assertStatus(200);

        $this->assertDatabaseMissing('cart', ['user_id' => 15, 'product_id' => 2]);
    }
}
