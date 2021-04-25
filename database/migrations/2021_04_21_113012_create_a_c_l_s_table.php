<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateACLSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('a_c_l_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('object');
            $table->integer('operation');
            $table->bigInteger('role_id')->unsigned();
            $table->foreign('role_id')
                ->references('id')
                ->on('employee_roles')
                ->onDelete('CASCADE');
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['object', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('a_c_l_s');
    }
}
