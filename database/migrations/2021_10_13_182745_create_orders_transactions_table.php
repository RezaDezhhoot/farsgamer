<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('status')->default('wait_for_confirm');
            $table->tinyInteger('is_returned')->default(0);
            $table->text('return_cause')->nullable();
            $table->text('return_images')->nullable();
            $table->string('note')->nullable();
            $table->decimal('commission',22)->default(0);
            $table->decimal('intermediary',22)->default(0);
            $table->timestamp('timer')->nullable();
            $table->integer('received_status')->default(0);
            $table->text('received_result')->nullable();
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
        Schema::dropIfExists('orders_transactions');
    }
}
