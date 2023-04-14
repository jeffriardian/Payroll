<?php

namespace Modules\Recruitment\Entities;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PphBonus extends Model
{
    use SoftDeletes;

    protected $table      = "pph_bonus";

    protected $fillable = [
        'id',
        'pph_id',
        'tax_allowance_thr_yearly',
        'tax_allowance_thr',
        'tax_allowance_bonus_yearly',
        'tax_allowance_bonus',
        'gross_yearly_bonus',
        'position_cost_yearly_bonus',
        'position_cost_monthly_bonus',
        'netto_yearly_bonus',
        'pph_thr_yearly',
        'pph_bonus_yearly',
        'pph_thr',
        'pph_bonus',
        'pph_bonus_thr',
        'deleted_at',
        'created_at',
        'updated_at'
    ];
}
