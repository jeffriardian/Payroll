<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayrollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('payroll');
        Schema::create('payroll', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('notrans');
            $table->string('kantor', 255);
            $table->string('bagian', 255)->nullable();
            $table->string('nrp', 255);
            $table->string('nik', 255);
            $table->string('nama', 255);
            $table->string('stapajak', 255);
            $table->string('npwp', 255)->nullable();
            $table->integer('jumlahhari');
            $table->integer('gapok');
            $table->integer('premihadir');
            $table->integer('premiprod');
            $table->integer('uangjabatan');
            $table->integer('bonus');
            $table->integer('gaji');
            $table->integer('jkm');
            $table->integer('jkk');
            $table->integer('bpjskes');
            $table->integer('bpjskeskar');
            $table->integer('h');
            $table->integer('s');
            $table->integer('i');
            $table->integer('a');
            $table->integer('c');
            $table->integer('off');
            $table->integer('st');
            $table->integer('telat_kurang');
            $table->integer('telat_lebih');
            $table->integer('setengah_hari');
            $table->integer('nilai_s');
            $table->integer('nilai_i');
            $table->integer('nilai_a');
            $table->integer('nilai_c');
            $table->integer('nilai_off');
            $table->integer('nilai_st');
            $table->integer('nilai_telat_kurang');
            $table->integer('nilai_telat_lebih');
            $table->integer('nilai_setengah_hari');
            $table->integer('totalpotonganabsensi');
            $table->float('jumlahlembur1');
            $table->integer('tariflembur1');
            $table->integer('uanglembur1');
            $table->float('jumlahlembur2');
            $table->integer('tariflembur2');
            $table->integer('uanglembur2');
            $table->float('jumlahlembur2minggu');
            $table->integer('tariflembur2minggu');
            $table->integer('uanglembur2minggu');
            $table->float('jumlahlembur3minggu');
            $table->integer('tariflembur3minggu');
            $table->integer('uanglembur3minggu');
            $table->integer('totallembur');
            $table->integer('bruto');
            $table->integer('potkoperasi')->nullable();
            $table->integer('potjhtkar')->nullable();
            $table->integer('potpensiunkar')->nullable();
            $table->integer('potlain');
            $table->integer('totalpotongan');
            $table->integer('netto');
            $table->string('norek', 50);
            $table->date('periode_awal');
            $table->date('periode_akhir');
            $table->date('date_created');

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
        Schema::dropIfExists('payroll');
    }
}
