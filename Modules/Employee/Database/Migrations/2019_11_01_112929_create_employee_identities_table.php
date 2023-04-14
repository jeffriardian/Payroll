<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeIdentitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_identities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('employee_nik', 10);
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('place_of_birth');
            $table->date('birthday');
            $table->boolean('gender');
            $table->integer('marital_status')->default(1);
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
        Schema::dropIfExists('employee_identities');
    }
}
