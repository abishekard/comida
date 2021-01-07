<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProductTableMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_table', function (Blueprint $table) {
            $table->id();
            $table->string('partner_id');
            $table->string('item_name');
            $table->string('item_image');
            $table->string('price');
            $table->string('price_type');
            $table->string('discount');
            $table->string('veg_non_veg');
            $table->string('type');
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
        Schema::dropIfExists('product_table');
    }
}
