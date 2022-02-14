<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTransactionDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders_transaction_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_transaction_id')->unique()->constrained('orders_transactions')->onDelete('cascade');
            $table->string('name');
            $table->longText('value')->nullable();
            $table->foreignId('send_id')->nullable()->constrained('sends')->onDelete('cascade');
            $table->string('transfer_result')->nullable();
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
        Schema::dropIfExists('orders_transaction_data');
    }
}
