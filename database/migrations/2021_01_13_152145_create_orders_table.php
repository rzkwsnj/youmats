<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('order_id')->unique();

            $table->bigInteger('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('NO ACTION')->onUpdate('CASCADE');

            $table->string('name');
            $table->string('email');

            $table->string('phone');
            $table->string('phone2')->nullable();

            $table->string('address');
            $table->string('building_number')->nullable();
            $table->string('street')->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();

            $table->string('payment_method');

            $table->string('reference_number')->nullable();
            $table->string('card_number')->nullable();
            $table->string('card_type')->nullable();
            $table->string('card_name')->nullable();
            $table->string('card_exp_date')->nullable();
            $table->timestamp('transaction_date')->nullable();

            $table->enum('payment_status', ['pending', 'refunded', 'completed']);
            $table->enum('status', ['pending', 'shipping', 'completed', 'refused']);

            $table->text('notes')->nullable();
            $table->text('refused_notes')->nullable();

            $table->string('coupon_code')->nullable();

            $table->double('total_price');

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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('orders');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
