<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class EnsureUsdCupProductPrices extends Migration
{
    public function up()
    {
        DB::table('products')
            ->orderBy('id')
            ->select(['id', 'price'])
            ->chunk(100, function ($products) {
                foreach ($products as $product) {
                    $usdPrice = DB::table('product_prices')
                        ->where('product_id', $product->id)
                        ->where('currency_code', 'USD')
                        ->value('price');
                    $cupPrice = DB::table('product_prices')
                        ->where('product_id', $product->id)
                        ->where('currency_code', 'CUP')
                        ->value('price');

                    if ($usdPrice === null) {
                        DB::table('product_prices')->insert([
                            'product_id' => $product->id,
                            'currency_code' => 'USD',
                            'price' => round($cupPrice !== null ? ((float) $cupPrice) / 24.48 : ((float) $product->price) / 7.2, 2),
                            'discount_price' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    if ($cupPrice === null) {
                        DB::table('product_prices')->insert([
                            'product_id' => $product->id,
                            'currency_code' => 'CUP',
                            'price' => round($usdPrice !== null ? ((float) $usdPrice) * 24.48 : ((float) $product->price) * 3.4, 2),
                            'discount_price' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            });
    }

    public function down()
    {
        // Auto-filled prices cannot be distinguished safely from edited prices.
    }
}
