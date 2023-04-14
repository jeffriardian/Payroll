<?php

namespace Modules\Recruitment\Entities\pph;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class ptkp extends Model
{
    use SoftDeletes;

    protected $table      = "ptkp";

    protected $fillable = [
        'code',
        'jumlah_ptkp',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    public function pph()
    {
        return $this->hasMany(pphdetail::class, 'ptkp_code', 'code');
    }
}
