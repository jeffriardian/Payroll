<?php

namespace Modules\Recruitment\Entities;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PesangonUpload extends Model
{
    use SoftDeletes;

    protected $table      = "pesangon_upload";

    protected $fillable = [
        'kantor',
        'nik',
        'nama',
        'pesangon',
        'nama_rekening',
        'norek',
        'status_pesangon',
        'deleted_at',
        'created_at',
        'updated_at'
    ];
}
