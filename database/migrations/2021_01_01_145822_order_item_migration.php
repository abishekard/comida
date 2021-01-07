<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrderItemMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_order_item', function (Blueprint $table) {
            $table->id();
            $table->string('order_id');
            $table->string('product_id');
            $table->integer('quantity');
            $table->integer('price');
            $table->integer('discount');
            $table->integer('item_name');
            $table->integer('item_image');
            $table->integer('pric_type');
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
        Schema::dropIfExists('customer_order_item');
    }
}
