<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePphTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pph', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nik', 15);
            $table->string('nama', 50);
            $table->string('ptkp_code', 10);
            $table->integer('tax_allowance_total')->default('0');
            $table->integer('pph_due')->default('0');
            $table->integer('pph_paid')->default('0');
            $table->string('periode_bulan', 5);
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
        Schema::dropIfExists('pph');
    }
}
