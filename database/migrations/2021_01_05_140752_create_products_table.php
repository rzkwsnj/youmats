<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('category_id')->unsigned()->index();
            $table->foreign('category_id')->references('id')->on('categories')
                ->onDelete('CASCADE')->onUpdate('CASCADE');

            $table->bigInteger('vendor_id')->unsigned()->index();
            $table->foreign('vendor_id')->references('id')->on('vendors')
                ->onDelete('CASCADE')->onUpdate('CASCADE');

            $table->text('name');
            $table->longText('desc')->nullable();
            $table->text('short_desc')->nullable();

            $table->decimal('rate', 10, 1)->default(5);
            $table->enum('type', ['product', 'service']);
            $table->decimal('cost', 10, 2)->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('stock')->default(0);

            $table->bigInteger('unit_id')->unsigned()->index()->nullable();
            $table->foreign('unit_id')->references('id')->on('units')
                ->onDelete('CASCADE')->onUpdate('CASCADE');

            $table->string('SKU')->unique();

            $table->json('attributes')->nullable();

            $table->tinyInteger('active')->default(1);
            $table->integer('views')->default(1);

            $table->bigInteger('shipping_id')->unsigned()->index()->nullable();
            $table->foreign('shipping_id')->references('id')->on('shippings')
                ->onDelete('CASCADE')->onUpdate('CASCADE');

            $table->boolean('specific_shipping')->default(0);
            $table->json('shipping_prices')->nullable();

            $table->text('search_keywords')->nullable();

            $table->string('slug')->unique();
            $table->text('meta_title')->nullable();
            $table->text('meta_desc')->nullable();
            $table->text('meta_keywords')->nullable();

            $table->integer('sort');
            $table->boolean('best_seller');

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
        Schema::dropIfExists('products');
    }
}
