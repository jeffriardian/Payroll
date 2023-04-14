<?php

namespace Modules\Recruitment\Entities;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    use SoftDeletes;

    protected $table      = "bonus";

    protected $fillable = [
        'kantor',
        'nik',
        'nama',
        'bonus',
        'nama_rekening',
        'norek',
        'periode_bulan',
        'periode_tahun',
        'deleted_at',
        'created_at',
        'updated_at'
    ];
}
