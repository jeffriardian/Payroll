<?php

namespace Modules\Recruitment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AbsensiPayrollStaff extends Model
{
    use SoftDeletes;

    protected $table      = "absensi_payroll_staff";

    protected $fillable = [
        'kantor',
        'nrp',
        'bagian',
        'nik',
        'nama',
        'npwp',
        'status_gaji',
        'stapajak',
        'jenis_gaji',
        'jhari',
        'gapok',
        'uang_hadir',
        'uang_prestasi',
        'bruto_gaji',
        'jkm',
        'jkk',
        'bpjskes',
        'bpjskeskar',
        'hari',
        'sakit',
        'ijin',
        'alpa',
        'cuti',
        'off',
        'st',
        'telat_kurang',
        'telat_lebih',
        'setengah_hari',
        'nilai_s',
        'nilai_i',
        'nilai_a',
        'nilai_c',
        'nilai_off',
        'nilai_st',
        'nilaitelatkurang',
        'nilaitelatlebih',
        'nilaisetengahhari',
        'totalpotonganabsensi',
        'jam_kerja',
        'lembur',
        'l1',
        'tarif_l1',
        'nilai_l1',
        'l2',
        'tarif_l2',
        'nilai_l2',
        'l2minggu',
        'tarifl2minggu',
        'nilail2minggu',
        'l3minggu',
        'tarifl3minggu',
        'nilail3minggu',
        'total_lembur',
        'koperasi',
        'jht',
        'pensiun',
        'total_potongan',
        'total_gaji',
        'nama_1',
        'norek',
        'status_payroll_staff',
    ];
}
