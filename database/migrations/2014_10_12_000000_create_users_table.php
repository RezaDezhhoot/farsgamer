<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name',30);
            $table->string('last_name',30);
            $table->string('user_name',50)->unique();
            $table->string('email',70)->unique();
            $table->string('province');
            $table->string('city');
            $table->string('phone',11)->unique();
            $table->string('code_id',10)->unique()->nullable();
            $table->string('status')->default('new');
            $table->string('profile_image')->nullable();
            $table->string('auth_image')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('pass_word')->nullable();
            $table->string('description')->nullable();
            $table->string('ip')->nullable();
            $table->integer('score')->default(0);
            $table->integer('order-transactions')->default(0);
            $table->integer('orders')->default(0);
            $table->timestamp('ban')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
