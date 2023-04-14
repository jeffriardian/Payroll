<?php


namespace Modules\Recruitment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Modules\Recruitment\Entities\parameterpph;

class tipepph extends Model
{
    use SoftDeletes;

    protected $table      = "tipe_pph";

    protected $fillable = [
        'id',
        'nama_tipe_pph',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    public function parameterpph()
    {
        return $this->hasMany(pphdetail::class, 'tipe_pph_id', 'id');
    }
}
