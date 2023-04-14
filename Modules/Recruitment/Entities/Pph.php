<?php

namespace Modules\Recruitment\Entities;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Modules\Recruitment\Entities\ptkp;

class pph extends Model
{
    use SoftDeletes;

    protected $table      = "pph";

    protected $fillable = [
        'id',
        'nik',
        'nama',
        'ptkp_code',
        'tax_allowance_total',
        'pph_due',
        'pph_paid',
        'periode_bulan',
        'periode_tahun',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    public function ptkp()
    {
        return $this->belongsTo(ptkp::class, 'ptkp_code', 'code');
    }
}
