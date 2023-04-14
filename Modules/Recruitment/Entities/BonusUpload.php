<?php

namespace Modules\Recruitment\Entities;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class BonusUpload extends Model
{
    use SoftDeletes;

    protected $table      = "bonus_upload";

    protected $fillable = [
        'kantor',
        'nik',
        'nama',
        'bonus',
        'nama_rekening',
        'norek',
        'status_bonus',
        'deleted_at',
        'created_at',
        'updated_at'
    ];
}
