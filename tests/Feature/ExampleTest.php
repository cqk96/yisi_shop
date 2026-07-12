<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_displays_products()
    {
        $category = Category::create([
            'name' => '测试分类',
            'slug' => 'test-category',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => '测试商品',
            'slug' => 'test-product',
            'description' => '测试商品描述',
            'price' => 99.00,
            'stock' => 10,
            'is_active' => true,
        ]);

        $product->skus()->create([
            'name' => '默认规格',
            'code' => 'TEST-SKU',
            'stock' => 10,
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('测试商品');
    }

    public function test_customer_can_checkout_cart_with_sku_stock()
    {
        $category = Category::create([
            'name' => '测试分类',
            'slug' => 'test-category',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => '测试商品',
            'slug' => 'test-product',
            'description' => '测试商品描述',
            'price' => 50.00,
            'stock' => 10,
            'is_active' => true,
        ]);

        $sku = $product->skus()->create([
            'name' => '红色',
            'code' => 'RED-001',
            'stock' => 10,
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $this->post(route('cart.items.store'), [
            'sku_id' => $sku->id,
            'quantity' => 2,
        ])->assertRedirect(route('cart.index'));

        $this->post(route('checkout.store'), [
            'customer_name' => '张三',
            'customer_email' => 'zhangsan@example.com',
            'customer_phone' => '13800138000',
            'shipping_address' => '上海市测试路 1 号',
        ])->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'customer_name' => '张三',
            'total' => 100.00,
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_name' => '测试商品',
            'sku_name' => '红色',
            'sku_code' => 'RED-001',
            'quantity' => 2,
        ]);

        $this->assertSame(8, $sku->fresh()->stock);
        $this->assertSame(8, $product->fresh()->stock);
        $this->assertCount(1, Order::all());
    }
}
