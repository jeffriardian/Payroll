<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAbsensiPayrollStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('absensi_payroll_staff', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kantor', 255);
            $table->string('nrp', 255);
            $table->string('bagian', 255)->nullable();
            $table->string('nik', 255);
            $table->string('nama', 255);
            $table->string('npwp', 255)->nullable();
            $table->string('status_gaji', 255);
            $table->string('stapajak', 255);
            $table->string('jenis_gaji', 255);
            $table->integer('jhari')->nullable()->default('0');
            $table->integer('gapok')->nullable()->default('0');
            $table->integer('uang_hadir')->nullable()->default('0');
            $table->integer('uang_prestasi')->nullable()->default('0');
            $table->integer('bruto_gaji')->nullable()->default('0');
            $table->integer('jkm')->nullable()->default('0');
            $table->integer('jkk')->nullable()->default('0');
            $table->integer('bpjskes')->nullable()->default('0');
            $table->integer('bpjskeskar')->nullable()->default('0');
            $table->integer('hari')->nullable()->default('0');
            $table->integer('sakit')->nullable()->default('0');
            $table->integer('ijin')->nullable()->default('0');
            $table->integer('alpa')->nullable()->default('0');
            $table->integer('cuti')->nullable()->default('0');
            $table->integer('off')->nullable()->default('0');
            $table->integer('st')->nullable()->default('0');
            $table->integer('telat_kurang')->nullable()->default('0');
            $table->integer('telat_lebih')->nullable()->default('0');
            $table->integer('setengah_hari')->nullable()->default('0');
            $table->integer('nilai_s')->nullable()->default('0');
            $table->integer('nilai_i')->nullable()->default('0');
            $table->integer('nilai_a')->nullable()->default('0');
            $table->integer('nilai_c')->nullable()->default('0');
            $table->integer('nilai_off')->nullable()->default('0');
            $table->integer('nilai_st')->nullable()->default('0');
            $table->integer('nilaitelatkurang')->nullable()->default('0');
            $table->integer('nilaitelatlebih')->nullable()->default('0');
            $table->integer('nilaisetengahhari')->nullable()->default('0');
            $table->integer('totalpotonganabsensi')->nullable()->default('0');
            $table->string('jam_kerja', 255)->nullable()->nullable();
            $table->string('lembur', 255)->nullable()->nullable();
            $table->float('l1')->nullable()->default('0');
            $table->integer('tarif_l1')->nullable()->default('0');
            $table->integer('nilai_l1')->nullable()->default('0');
            $table->float('l2')->nullable()->default('0');
            $table->integer('tarif_l2')->nullable()->default('0');
            $table->integer('nilai_l2')->nullable()->default('0');
            $table->float('l2minggu')->nullable()->default('0');
            $table->integer('tarifl2minggu')->nullable()->default('0');
            $table->integer('nilail2minggu')->nullable()->default('0');
            $table->float('l3minggu')->nullable()->default('0');
            $table->integer('tarifl3minggu')->nullable()->default('0');
            $table->integer('nilail3minggu')->nullable()->default('0');
            $table->integer('total_lembur')->nullable()->default('0');
            $table->integer('koperasi')->nullable()->default('0');
            $table->integer('jht')->nullable()->default('0');
            $table->integer('pensiun')->nullable()->default('0');
            $table->integer('total_potongan')->nullable()->default('0');
            $table->integer('total_gaji')->nullable()->default('0');
            $table->string('nama_1', 50);
            $table->string('norek', 50);
            $table->integer('status_payroll_staff')->default('0');
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
        Schema::dropIfExists('absensi_payroll_staff');
    }
}
