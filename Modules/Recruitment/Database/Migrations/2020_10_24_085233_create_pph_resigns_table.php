<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePphResignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pph_resign', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('pph_id');
            $table->integer('tax_allowance_resign')->default('0');
            $table->integer('pph_resign')->default('0');
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
        Schema::dropIfExists('pph_resigns');
    }
}
