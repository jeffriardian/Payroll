<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSptTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('spt');
        Schema::create('spt', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('nrp');
            $table->string('nik', 15);
            $table->string('nama', 50);
            $table->string('ptkp_code', 10);
            $table->integer('gross_yearly')->default('0');
            $table->integer('position_cost_yearly')->default('0');
            $table->integer('jht_employee_yearly')->default('0');
            $table->integer('jp_employee_yearly')->default('0');
            $table->integer('jht_yearly')->default('0');
            $table->integer('netto_yearly')->default('0');
            $table->integer('ptkp');
            $table->integer('pkp')->default('0');
            $table->integer('tax_allowance_salary')->default('0');
            $table->integer('pph_salary')->default('0');
            $table->integer('tax_allowance_bonus')->default('0');
            $table->integer('pph_bonus')->default('0');
            $table->integer('tax_allowance_thr')->default('0');
            $table->integer('pph_thr')->default('0');
            $table->integer('tax_allowance_pesangon')->default('0');
            $table->integer('pph_pesangon')->default('0');
            $table->integer('tax_allowance_yearly')->default('0');
            $table->integer('pph_paid')->default('0');
            $table->integer('pph_due')->default('0');
            $table->string('periode_tahun', 5);
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
        Schema::dropIfExists('spt');
    }
}
