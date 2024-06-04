<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdjustOrdersQuotes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->after('coupon_code', function (Blueprint $t) {
                $t->double('subtotal');
                $t->double('delivery');
            });
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->after('price', function (Blueprint $t) {
                $t->double('delivery')->default(0);
                $t->text('delivery_cars')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
