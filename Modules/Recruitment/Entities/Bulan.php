<?php

namespace Modules\Recruitment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class bulan extends Model
{
    use SoftDeletes;

    protected $table      = "bulan";

    protected $fillable = [
        'value',
        'text',
    ];
}
