<?php

namespace Modules\Recruitment\Entities;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ThrUpload extends Model
{
    use SoftDeletes;

    protected $table      = "thr_upload";

    protected $fillable = [
        'kantor',
        'nik',
        'nama',
        'thr',
        'nama_rekening',
        'norek',
        'status_thr',
        'deleted_at',
        'created_at',
        'updated_at'
    ];
}
