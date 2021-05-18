<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutgoingStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outgoing_stocks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('quantity');
            $table->dateTime('date')->useCurrent();
            $table->enum('category', ['waste', 'sold']);
            $table->bigInteger('ingredient_id')->unsigned();
            $table->foreign('ingredient_id')
                ->references('id')
                ->on('ingredients')
                ->onDelete('CASCADE');
            $table->bigInteger('employee_id')->unsigned();
            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->onDelete('CASCADE');
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
        Schema::dropIfExists('outgoing_stocks');
    }
}
