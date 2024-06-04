<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuoteItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quote_items', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('quote_id')->unsigned()->index();
            $table->foreign('quote_id')->references('id')->on('quotes')
                ->onDelete('CASCADE')->onUpdate('CASCADE');

            $table->bigInteger('product_id')->unsigned()->index();
            $table->foreign('product_id')->references('id')->on('products')
                ->onDelete('NO ACTION')->onUpdate('CASCADE');

            $table->string('product_name');

            $table->bigInteger('vendor_id')->unsigned()->index()->nullable();
            $table->foreign('vendor_id')->references('id')->on('vendors')
                ->onDelete('NO ACTION')->onUpdate('CASCADE');

            $table->string('vendor_name')->nullable();

            $table->tinyInteger('quantity');

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
        Schema::dropIfExists('quote_items');
    }
}
