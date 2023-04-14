<?php

namespace Modules\Recruitment\Entities;

use Illuminate\Database\Eloquent\Model;

class gaji extends Model
{
    protected $connection = "sqlsrv";

    protected $database = "personalia";

    protected $table = "dbo.Gaji";

    protected $fillable = [
        'notran',
        'dari',
        'sampai',
        'jns',
        'tglbuat',
        'HARIX',
        'sts',
        'jgaji',
        'kodeprs',
        'adaaoc',
    ];
}
