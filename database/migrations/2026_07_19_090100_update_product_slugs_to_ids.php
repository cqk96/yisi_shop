<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateProductSlugsToIds extends Migration
{
    public function up()
    {
        DB::table('products')
            ->orderBy('id')
            ->select(['id'])
            ->chunk(100, function ($products) {
                foreach ($products as $product) {
                    DB::table('products')
                        ->where('id', $product->id)
                        ->update(['slug' => (string) $product->id]);
                }
            });
    }

    public function down()
    {
        // Slugs generated from names cannot be restored reliably.
    }
}
