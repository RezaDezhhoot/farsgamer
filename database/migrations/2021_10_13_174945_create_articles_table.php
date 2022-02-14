<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('slug',120)->unique();
            $table->string('title',120);
            $table->string('main_image')->unique();
            $table->longText('content');
            $table->string('seo_keywords')->nullable();
            $table->string('seo_description')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->integer('view_count')->default(0);
            $table->float('score')->default(0);
            $table->string('status')->default('new');
            $table->tinyInteger('commentable')->default(1);
            $table->tinyInteger('google_indexing')->default(1);
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
        Schema::dropIfExists('articles');
    }
}
