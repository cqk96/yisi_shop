<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageUrlToProductSkusTable extends Migration
{
    public function up()
    {
        Schema::table('product_skus', function (Blueprint $table) {
            $table->string('image_url')->nullable()->after('code');
        });
    }

    public function down()
    {
        Schema::table('product_skus', function (Blueprint $table) {
            $table->dropColumn('image_url');
        });
    }
}
