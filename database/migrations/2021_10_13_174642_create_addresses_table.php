<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('country');
            $table->string('province');
            $table->string('city');
            $table->string('address');
            $table->string('postal_code');
            $table->string('first_name',30);
            $table->string('last_name',30);
            $table->string('phone',11);
            $table->string('email');
            $table->string('status')->default('confirmed');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');;
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
        Schema::dropIfExists('addresses');
    }
}
