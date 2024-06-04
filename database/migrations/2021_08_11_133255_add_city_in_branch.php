<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCityInBranch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendor_branches', function (Blueprint $table) {
            $table->after('vendor_id', function($table) {
                $table->bigInteger('city_id')->unsigned()->index();
                $table->foreign('city_id')->references('id')->on('cities')->onDelete('NO ACTION')->onUpdate('CASCADE');
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
        Schema::table('branch', function (Blueprint $table) {
            //
        });
    }
}
