<?php

namespace Modules\Recruitment\Entities;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Thr extends Model
{
    use SoftDeletes;

    protected $table      = "thr";

    protected $fillable = [
        'kantor',
        'nik',
        'nama',
        'thr',
        'nama_rekening',
        'norek',
        'periode_bulan',
        'periode_tahun',
        'deleted_at',
        'created_at',
        'updated_at'
    ];
}
