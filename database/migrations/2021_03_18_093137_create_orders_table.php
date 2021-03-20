<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('total_menu')->default(0);
            $table->integer('total_item')->default(0);
            $table->dateTime('order_date')->useCurrent();
            $table->dateTime('finish_at')->nullable();
            $table->bigInteger('reservation_id')->unsigned();
            $table->foreign('reservation_id')
                ->references('id')
                ->on('reservations')
                ->onDelete('RESTRICT');
            $table->bigInteger('waiter_id')->unsigned();
            $table->foreign('waiter_id')
                ->references('id')
                ->on('employees')
                ->onDelete('RESTRICT');
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
        Schema::dropIfExists('orders');
    }
}
