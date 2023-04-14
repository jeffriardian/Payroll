<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePphSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pph_salary', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('pph_id');
            $table->integer('tax_allowance_yearly')->default('0');
            $table->integer('tax_allowance')->default('0');
            $table->integer('gross_monthly')->default('0');
            $table->integer('gross_yearly')->default('0');
            $table->integer('position_cost_yearly')->default('0');
            $table->integer('position_cost_monthly')->default('0');
            $table->integer('jht_employee_yearly')->default('0');
            $table->integer('jp_employee_yearly')->default('0');
            $table->integer('netto_yearly')->default('0');
            $table->integer('ptkp');
            $table->integer('pkp')->default('0');
            $table->integer('pph_yearly')->default('0');
            $table->integer('pph_salary')->default('0');
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
        Schema::dropIfExists('pph_salary');
    }
}
