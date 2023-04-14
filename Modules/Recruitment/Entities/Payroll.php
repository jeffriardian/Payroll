<?php

namespace Modules\Recruitment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Recruitment\Entities\PeriodePayroll;

class Payroll extends Model
{
    use SoftDeletes;

    protected $table      = "payroll";

    protected $fillable = [
        'id',
        'notrans',
        'kantor',
        'bagian',
        'nrp',
        'nik',
        'nama',
        'stapajak',
        'npwp',
        'jumlahhari',
        'gapok',
        'premihadir',
        'premiprod',
        'uangjabatan',
        'bonus',
        'gaji',
        'jkm',
        'jkk',
        'bpjskes',
        'bpjskeskar',
        'h',
        's',
        'i',
        'a',
        'c',
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
        'nilai_telat_kurang',
        'nilai_telat_lebih',
        'nilai_setengah_hari',
        'totalpotonganabsensi',
        'jumlahlembur1',
        'tariflembur1',
        'uanglembur1',
        'jumlahlembur2',
        'tariflembur2',
        'uanglembur2',
        'jumlahlembur2minggu',
        'tariflembur2minggu',
        'uanglembur2minggu',
        'jumlahlembur3minggu',
        'tariflembur3minggu',
        'uanglembur3minggu',
        'totallembur',
        'bruto',
        'potkoperasi',
        'potjhtkar',
        'potpensiunkar',
        'potlain',
        'totalpotongan',
        'netto',
		'norek',
        'periode_awal',
        'periode_akhir',
        'date_created',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    public function PeriodePayroll()
    {
        return $this->belongsTo(PeriodePayroll::class, 'notrans', 'notrans');
    }
}
