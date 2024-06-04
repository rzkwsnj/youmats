<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('order_id')->unsigned()->index();
            $table->foreign('order_id')->references('id')->on('orders')
                ->onDelete('CASCADE')->onUpdate('CASCADE');

            $table->bigInteger('product_id')->unsigned()->index();
            $table->foreign('product_id')->references('id')->on('products')
                ->onDelete('NO ACTION')->onUpdate('CASCADE');

            $table->bigInteger('vendor_id')->unsigned()->index();
            $table->foreign('vendor_id')->references('id')->on('vendors')
                ->onDelete('NO ACTION')->onUpdate('CASCADE');

            $table->string('vendor_name');

            $table->string('status')->default('pending');
            $table->string('payment_status')->default('pending');
            $table->string('refused_note')->nullable();

            $table->smallInteger('quantity');
            $table->double('price');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items');
    }
}
