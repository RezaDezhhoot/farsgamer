<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatsGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chats_groups', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->foreignId('user1')->constrained('users')->onDelete('cascade');
            $table->foreignId('user2')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('open');
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
        Schema::dropIfExists('chats_groups');
    }
}
