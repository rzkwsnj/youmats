<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesMembershipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories_memberships', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('category_id')->unsigned()->index();
            $table->foreign('category_id')->references('id')->on('categories')
                ->onDelete('NO ACTION')->onUpdate('CASCADE');

            $table->bigInteger('membership_id')->unsigned()->index();
            $table->foreign('membership_id')->references('id')->on('memberships')
                ->onDelete('NO ACTION')->onUpdate('CASCADE');

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
        Schema::dropIfExists('categories_memberships');
    }
}
