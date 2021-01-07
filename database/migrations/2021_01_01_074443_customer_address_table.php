<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CustomerAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customerAddressTable', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('address');
            $table->string('state');
            $table->string('city');
            $table->string('pincode');
            $table->string('landmark');
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
        Schema::dropIfExists('customerAddressTable');
    }
}
