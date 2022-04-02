<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffendsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offends', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->text('content');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('phone',11);
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
        Schema::dropIfExists('offends');
    }
}
