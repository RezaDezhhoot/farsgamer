<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug',70)->unique();
            $table->string('title',70);
            $table->string('logo')->nullable();
            $table->string('default_image')->nullable();
            $table->string('slider')->nullable();
            $table->text('description')->nullable();
            $table->string('seo_keywords');
            $table->string('seo_description');
            $table->decimal('guarantee_time',65);
            $table->decimal('send_time',65)->nullable();
            $table->decimal('pay_time',65)->nullable();
            $table->decimal('receive_time',65)->nullable();
            $table->decimal('sending_data_time',65)->nullable();
            $table->decimal('no_receive_time',65)->nullable();
            $table->integer('parent_id')->nullable();
            $table->string('status')->default('available');
            $table->string('is_available')->default('yes');
            $table->string('type')->default('digital');
            $table->tinyInteger('control')->default(0);
            $table->longText('forms')->nullable();
            $table->decimal('commission',22,2)->default(0);
            $table->decimal('intermediary',22,2)->default(0);
            $table->softDeletes();
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
        Schema::dropIfExists('categories');
    }
}
