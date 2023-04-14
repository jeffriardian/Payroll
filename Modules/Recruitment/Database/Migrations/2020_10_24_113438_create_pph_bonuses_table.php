<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePphBonusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pph_bonus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('pph_id');
            $table->integer('tax_allowance_thr_yearly')->default('0');
            $table->integer('tax_allowance_thr')->default('0');
            $table->integer('tax_allowance_bonus_yearly')->default('0');
            $table->integer('tax_allowance_bonus')->default('0');
            $table->integer('gross_yearly_bonus')->default('0');
            $table->integer('position_cost_yearly_bonus')->default('0');
            $table->integer('position_cost_monthly_bonus')->default('0');
            $table->integer('netto_yearly_bonus')->default('0');
            $table->integer('pph_thr_yearly')->default('0');
            $table->integer('pph_bonus_yearly')->default('0');
            $table->integer('pph_thr')->default('0');
            $table->integer('pph_bonus')->default('0');
            $table->integer('pph_bonus_thr')->default('0');
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
        Schema::dropIfExists('pph_bonus');
    }
}
