<?php

namespace Modules\Recruitment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollComponent extends Model
{
    use SoftDeletes;

    protected $table      = "payroll_components";

    protected $fillable = [
        'code',
        'name',
        'is_active',
    ];
}
