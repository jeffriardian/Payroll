<?php

namespace Modules\Recruitment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Recruitment\Entities\Payroll;

class PeriodePayroll extends Model
{
    use SoftDeletes;

    protected $table      = "periode_payroll";

    protected $fillable = [
        'notrans',
        'periode_awal',
        'periode_akhir',
        'date_created',
        'kodeprs',
        'status_payroll',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    public function Payroll()
    {
        return $this->hasMany(Payroll::class, 'notrans', 'notrans');
    }
}
