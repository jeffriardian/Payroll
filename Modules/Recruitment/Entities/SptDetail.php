<?php

namespace Modules\Recruitment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Recruitment\Entities\spt;
use Modules\Recruitment\Entities\parameterpph;

class SptDetail extends Model
{
    use SoftDeletes;

    protected $table      = "spt_detail";

    protected $fillable = [
        'id',
        'spt_id',
        'parameter_id',
        'jumlah',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    public function spt()
    {
        return $this->belongsTo(spt::class, 'spt_id', 'id');
    }

    public function parameterpph()
    {
        return $this->belongsTo(parameterpph::class, 'parameter_id', 'id');
    }
}
