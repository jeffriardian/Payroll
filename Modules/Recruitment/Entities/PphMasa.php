<?php

namespace Modules\Recruitment\Entities;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PphMasa extends Model
{
    use SoftDeletes;

    protected $table      = "pph_masa";

    protected $fillable = [
        'id',
        'pph_id',
        'tax_allowance_yearly',
        'tax_allowance',
        'gross_monthly',
        'gross_yearly',
        'position_cost_yearly',
        'position_cost_monthly',
        'jht_employee_yearly',
        'jp_employee_yearly',
        'netto_yearly',
        'ptkp',
        'pkp',
        'pph_masa_yearly',
        'pph_paid',
        'pph_masa',
        'deleted_at',
        'created_at',
        'updated_at'
    ];
}
