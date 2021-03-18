<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('phone');
            $table->dateTime('date_join');
            $table->boolean('is_disabled')->default(false);
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->bigInteger('employee_roles_id')->unsigned();
            $table->foreign('employee_roles_id')
                ->references('id')
                ->on('employee_roles')
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
        Schema::dropIfExists('employees');
    }
}
