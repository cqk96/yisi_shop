<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class NormalizeProductPricesToUsdCup extends Migration
{
    public function up()
    {
        DB::table('product_prices')
            ->whereNotIn('currency_code', ['USD', 'CUP'])
            ->delete();

        DB::table('products')
            ->orderBy('id')
            ->select(['id', 'price'])
            ->chunk(100, function ($products) {
                foreach ($products as $product) {
                    $hasCupPrice = DB::table('product_prices')
                        ->where('product_id', $product->id)
                        ->where('currency_code', 'CUP')
                        ->exists();

                    if ($hasCupPrice) {
                        continue;
                    }

                    DB::table('product_prices')->insert([
                        'product_id' => $product->id,
                        'currency_code' => 'CUP',
                        'price' => round(((float) $product->price) * 3.4, 2),
                        'discount_price' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });
    }

    public function down()
    {
        // Removed currencies cannot be restored reliably.
    }
}
