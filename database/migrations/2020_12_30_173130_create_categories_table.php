<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            $table->text('name');
            $table->text('desc')->nullable();
            $table->text('short_desc')->nullable();

            $table->string('slug')->unique();
            $table->text('meta_title')->nullable();
            $table->text('meta_desc')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->text('schema')->nullable();

            $table->boolean('isFeatured')->default(0);
            $table->boolean('topCategory')->default(0);
            $table->boolean('show_in_footer')->default(0);

            $table->boolean('section_i')->default(0);
            $table->boolean('section_ii')->default(0);
            $table->boolean('section_iii')->default(0);
            $table->boolean('section_iv')->default(0);

            $table->integer('sort');
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
        Schema::dropIfExists('categories');
    }
}
