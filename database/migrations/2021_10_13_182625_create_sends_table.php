<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSendsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sends', function (Blueprint $table) {
            $table->id();
            $table->string('slug',50)->unique();
            $table->string('logo');
            $table->float('send_time_inner_city')->comment('hour');
            $table->float('send_time_outer_city')->comment('hour');
            $table->text('note')->nullable();
            $table->tinyInteger('pursuit')->default(1);
            $table->string('status')->default('available');
            $table->string('pursuit_web_site')->nullable();
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
        Schema::dropIfExists('sends');
    }
}
