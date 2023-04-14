<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSptDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('spt_detail');
        Schema::create('spt_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('spt_id');
            $table->bigInteger('parameter_id');
            $table->integer('jumlah')->default('0');
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
        Schema::dropIfExists('spt_detail');
    }
}
