<?php

namespace Modules\Recruitment\Entities;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PphResign extends Model
{
    use SoftDeletes;

    protected $table      = "pph_resign";

    protected $fillable = [
        'id',
        'pph_id',
        'tax_allowance_resign',
        'pph_resign',
        'deleted_at',
        'created_at',
        'updated_at'
    ];
}
