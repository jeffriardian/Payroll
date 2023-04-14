<?php

namespace Modules\Recruitment\Entities;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Recruitment\Entities\ptkp;
use Illuminate\Database\Eloquent\Model;

class Spt extends Model
{
    use SoftDeletes;

    protected $table      = "spt";

    protected $fillable = [
        'id',
        'nrp',
        'nik',
        'nama',
        'ptkp_code',
        'gross_yearly',
        'position_cost_yearly',
        'jht_employee_yearly',
        'jp_employee_yearly',
        'jht_yearly',
        'netto_yearly',
        'ptkp',
        'pkp',
        'tax_allowance_salary',
        'pph_salary',
        'tax_allowance_bonus',
        'pph_bonus',
        'tax_allowance_thr',
        'pph_thr',
        'tax_allowance_pesangon',
        'pph_pesangon',
        'tax_allowance_yearly',
        'pph_due',
        'pph_paid',
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
