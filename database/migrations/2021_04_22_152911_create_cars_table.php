<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('driver_id')->unsigned()->index();
            $table->foreign('driver_id')->references('id')->on('drivers')
                ->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->bigInteger('type_id')->unsigned()->index();
            $table->foreign('type_id')->references('id')->on('car_types')
                ->onDelete('CASCADE')->onUpdate('CASCADE');

            $table->text('name')->nullable();
            $table->string('model');
            $table->string('license_no');
            $table->integer('max_load');
            $table->decimal('price_per_kilo', 10, 2);

            $table->tinyInteger('active')->default(1);
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
        Schema::dropIfExists('cars');
    }
}
