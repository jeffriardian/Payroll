<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParameterPphsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('parameter_pph');
        Schema::create('parameter_pph', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('parameter_code', 50);
            $table->string('nama_parameter_pph', 50);
            $table->bigInteger('tipe_pph_id');
            $table->string('status_parameter_pph', 50);
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
        Schema::dropIfExists('parameter_pph');
    }
}
