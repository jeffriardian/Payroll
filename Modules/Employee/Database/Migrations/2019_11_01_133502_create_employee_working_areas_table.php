<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeWorkingAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_working_areas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('employee_nik', 10);
            $table->string('company_working_area_code', 8);
            $table->string('company_position_code', 8);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_current_working_area')->default(false);
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
        Schema::dropIfExists('employee_working_areas');
    }
}
