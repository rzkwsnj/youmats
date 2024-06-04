<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class EditLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->after('city', function (Blueprint $t) {
                $t->text('coordinates')->nullable();
            });
            $table->after('page_id', function (Blueprint $t) {
                $t->bigInteger('vendor_id')->nullable()->unsigned();
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
        Schema::table('logs', function (Blueprint $table) {
            $table->dropColumn(['coordinates', 'vendor_id']);
        });
    }
}
