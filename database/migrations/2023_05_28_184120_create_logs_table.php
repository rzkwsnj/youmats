<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id()->index();

            $table->ipAddress('ip')->index();
            $table->string('country', 50)->index()->nullable();
            $table->string('city', 50)->index()->nullable();

            $table->string('url', 255)->index();

            $table->enum('type', ['visit', 'chat', 'call', 'email'])->index();

            $table->nullableMorphs('page');

            $table->boolean('is_subscribed')->default(0)->index();

            $table->timestamp('created_at', 0)->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logs');
    }
}
