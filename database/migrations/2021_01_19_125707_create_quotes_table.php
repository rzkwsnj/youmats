<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('quote_no')->unique();

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

            $table->enum('status', ['pending', 'shipping', 'completed', 'refused']);

            $table->text('notes')->nullable();

            $table->double('estimated_price')->nullable();

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
        Schema::dropIfExists('quotes');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
