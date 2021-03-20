<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('transaction_sn');
            $table->integer('subtotal');
            $table->integer('tax');
            $table->integer('service');
            $table->integer('grand_total');
            $table->enum('payment_method', ['cash', 'debit', 'credit']);
            $table->string('edc_verification');
            $table->bigInteger('order_id')->unsigned();
            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onDelete('CASCADE');
            $table->bigInteger('transaction_card_id')->unsigned();
            $table->foreign('transaction_card_id')
                ->references('id')
                ->on('transaction_cards')
                ->onDelete('RESTRICT');
            $table->bigInteger('cashier_id')->unsigned();
            $table->foreign('cashier_id')
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
        Schema::dropIfExists('transactions');
    }
}
