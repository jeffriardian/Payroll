<?php

namespace Modules\Recruitment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Modules\Recruitment\Entities\tipepph;
use Modules\Recruitment\Entities\pphdetail;

class parameterpph extends Model
{
    use SoftDeletes;

    protected $table      = "parameter_pph";

    protected $fillable = [
        'id',
        'parameter_code',
        'nama_parameter_pph',
        'tipe_pph_id',
        'status_parameter_pph',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    public function pphdetail()
    {
        return $this->hasMany(pphdetail::class, 'pph_id', 'id');
    }

    public function tipepph()
    {
        return $this->belongsTo(tipepph::class, 'tipe_pph_id', 'id');
    }
}
