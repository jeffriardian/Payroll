<?php

namespace Modules\Recruitment\Entities;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Pesangon extends Model
{
    use SoftDeletes;

    protected $table      = "pesangon";

    protected $fillable = [
        'kantor',
        'nik',
        'nama',
        'pesangon',
        'nama_rekening',
        'norek',
        'periode_bulan',
        'periode_tahun',
        'deleted_at',
        'created_at',
        'updated_at'
    ];
}
