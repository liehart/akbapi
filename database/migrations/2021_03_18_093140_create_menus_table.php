<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('description');
            $table->enum('menu_type', ['side_dish', 'drink', 'main']);
            $table->string('unit');
            $table->boolean('is_available')->default(false);
            $table->integer('price');
            $table->integer('serving_size');
            $table->bigInteger('ingredient_id')->unsigned();
            $table->foreign('ingredient_id')
                ->references('id')
                ->on('ingredients')
                ->onDelete('RESTRICT');
            $table->string('image_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menus');
    }
}
