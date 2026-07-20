<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class NormalizeLocalStorageUrls extends Migration
{
    public function up()
    {
        foreach ([
            'products' => ['image_url'],
            'product_images' => ['image_url'],
            'product_skus' => ['image_url'],
        ] as $table => $columns) {
            foreach ($columns as $column) {
                DB::table($table)
                    ->where($column, 'like', 'http://localhost/storage/%')
                    ->update([
                        $column => DB::raw("REPLACE({$column}, 'http://localhost/storage/', '/storage/')"),
                    ]);

                DB::table($table)
                    ->where($column, 'like', 'http://127.0.0.1:8000/storage/%')
                    ->update([
                        $column => DB::raw("REPLACE({$column}, 'http://127.0.0.1:8000/storage/', '/storage/')"),
                    ]);
            }
        }
    }

    public function down()
    {
        // Relative storage URLs are portable across local and server domains.
    }
}
