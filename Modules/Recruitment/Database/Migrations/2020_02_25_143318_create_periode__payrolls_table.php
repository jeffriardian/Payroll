<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePeriodePayrollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('periode_payroll');
        Schema::create('periode_payroll', function (Blueprint $table) {
            $table->bigIncrements('notrans');
            $table->date('periode_awal', 50);
            $table->date('periode_akhir', 50);
            $table->date('date_created');
            $table->string('kodeprs', 5);
            $table->string('status_payroll', 10);
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
        Schema::dropIfExists('periode_payroll');
    }
}
