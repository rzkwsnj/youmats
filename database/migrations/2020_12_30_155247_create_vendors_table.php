<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('country_id')->unsigned()->index();
            $table->foreign('country_id')->references('id')->on('countries')
                ->onDelete('NO ACTION')->onUpdate('CASCADE');

//            $table->bigInteger('category_id')->unsigned()->index();
//            $table->foreign('category_id')->references('id')->on('categories')
//                ->onDelete('CASCADE')->onUpdate('CASCADE');

            $table->text('name');
            $table->string('email')->unique();

            $table->string('phone')->nullable();
            $table->string('phone2')->nullable();
            $table->string('whatsapp_phone')->nullable();

            $table->string('address')->nullable();
            $table->string('address2')->nullable();

            $table->text('latitude')->nullable();
            $table->text('longitude')->nullable();

            $table->string('facebook_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('pinterest_url')->nullable();
            $table->string('website_url')->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();

            $table->string('slug')->unique();
            $table->text('meta_title')->nullable();
            $table->text('meta_desc')->nullable();
            $table->text('meta_keywords')->nullable();

            $table->boolean('isFeatured')->default(0);

            $table->tinyInteger('active')->default(0);

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
        Schema::dropIfExists('vendors');
    }
}
