<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBonusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bonus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kantor', 20);
            $table->string('nik', 20);
            $table->string('nama', 50);
            $table->integer('bonus');
            $table->string('nama_rekening', 50);
            $table->string('norek', 50);
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
        Schema::dropIfExists('bonus');
    }
}
