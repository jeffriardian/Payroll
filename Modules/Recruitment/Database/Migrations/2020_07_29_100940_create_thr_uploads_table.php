<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThrUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thr_upload', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kantor', 20);
            $table->string('nik', 20);
            $table->string('nama', 50);
            $table->integer('thr');
            $table->string('nama_rekening', 50);
            $table->string('norek', 50);
            $table->integer('status_thr')->default('1');

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
        Schema::dropIfExists('thr_upload');
    }
}
