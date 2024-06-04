<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeCategoryIdInProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_prefix = DB::getTablePrefix();

        DB::statement('ALTER TABLE `' . $table_prefix . 'products` MODIFY `category_id` BIGINT UNSIGNED NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table_prefix = DB::getTablePrefix();

        DB::statement('ALTER TABLE `' . $table_prefix . 'products` MODIFY `category_id` BIGINT UNSIGNED NOT NULL;');
    }
}
