<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('amount');
            $table->string('payment_gateway');
            $table->string('payment_token')->unique();
            $table->string('payment_ref')->unique()->nullable();
            $table->morphs('model');
            $table->string('status_code')->nullable();
            $table->text('json')->nullable();
            $table->string('status_message')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->string('call_back_url');
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
        Schema::dropIfExists('payments');
    }
}
