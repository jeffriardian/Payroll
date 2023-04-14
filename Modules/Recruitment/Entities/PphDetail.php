<?php

namespace Modules\Recruitment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Modules\Recruitment\Entities\pph;
use Modules\Recruitment\Entities\parameterpph;

class pphdetail extends Model
{
    use SoftDeletes;

    protected $table      = "pph_detail";

    protected $fillable = [
        'id',
        'pph_id',
        'parameter_id',
        'jumlah',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    public function pph()
    {
        return $this->belongsTo(pph::class, 'pph_id', 'id');
    }

    public function parameterpph()
    {
        return $this->belongsTo(parameterpph::class, 'parameter_id', 'id');
    }
}
