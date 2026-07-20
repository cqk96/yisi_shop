<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RemoveOrphanProductPrices extends Migration
{
    public function up()
    {
        $productIds = DB::table('products')->pluck('id')->all();

        if (empty($productIds)) {
            DB::table('product_prices')->delete();

            return;
        }

        DB::table('product_prices')
            ->whereNotIn('product_id', $productIds)
            ->delete();
    }

    public function down()
    {
        // Deleted orphan price rows cannot be restored reliably.
    }
}
