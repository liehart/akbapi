<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_cards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('cardholder_name')->nullable();
            $table->string('cardholder_number');
            $table->integer('cardholder_exp_month');
            $table->integer('cardholder_exp_year');
            $table->enum('card_type', ['debit', 'credit']);
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
        Schema::dropIfExists('transaction_cards');
    }
}
