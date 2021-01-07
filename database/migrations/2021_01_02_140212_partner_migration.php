<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PartnerMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('profile_image');
            $table->string('email');
            $table->string('mobile');
            $table->string('password');
            $table->string('shop_name')->nullable();
            $table->string('shop_image')->nullable();
            $table->string('address')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('pincode')->nullable();
            $table->string('lat')->nullable();
            $table->string('long')->nullable();
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->string('available')->nullable();
            $table->string('rating')->nullable();
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
        Schema::dropIfExists('partner');
    }
}
