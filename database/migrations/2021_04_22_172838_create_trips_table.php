<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->bigInteger('driver_id')->unsigned()->nullable();
            $table->foreign('driver_id')->references('id')->on('drivers')
                ->onDelete('CASCADE')->onUpdate('CASCADE');

            $table->string('pickup_latitude');
            $table->string('pickup_longitude');
            $table->string('destination_latitude');
            $table->string('destination_longitude');
            $table->float('distance');
            $table->decimal('price', 10, 2)->nullable();

            $table->enum('driver_status', [0, 1])->comment('0 / Pending, 1 / Accepted')->default(0);
            $table->enum('status', [0, 1, 2])->comment('0 / Pending, 1 / In progress, 2 / Completed')->default(0);

            $table->timestamp('started_at')->nullable();
            $table->decimal('user_rate', 10, 1)->nullable();
            $table->text('user_review')->nullable();
            $table->decimal('driver_rate', 10, 1)->nullable();
            $table->text('driver_review')->nullable();

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
        Schema::dropIfExists('trips');
    }
}
