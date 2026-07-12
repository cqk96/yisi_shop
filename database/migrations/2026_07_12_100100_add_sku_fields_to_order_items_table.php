<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSkuFieldsToOrderItemsTable extends Migration
{
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('product_sku_id')->nullable()->after('product_id')->constrained('product_skus')->nullOnDelete();
            $table->string('sku_name')->nullable()->after('product_name');
            $table->string('sku_code')->nullable()->after('sku_name');
        });
    }

    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_sku_id']);
            $table->dropColumn(['product_sku_id', 'sku_name', 'sku_code']);
        });
    }
}
