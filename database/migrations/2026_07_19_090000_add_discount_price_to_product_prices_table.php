<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddDiscountPriceToProductPricesTable extends Migration
{
    public function up()
    {
        Schema::table('product_prices', function (Blueprint $table) {
            $table->decimal('discount_price', 10, 2)->nullable()->after('price');
        });

        DB::table('product_prices')->where('currency_code', 'HKD')->delete();
    }

    public function down()
    {
        Schema::table('product_prices', function (Blueprint $table) {
            $table->dropColumn('discount_price');
        });
    }
}
