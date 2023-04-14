<?php

namespace Modules\Company\Entities;

use Illuminate\Database\Eloquent\Model;

class PositionLevel extends Model
{
    protected $table = 'company_position_levels';

    protected $fillable = [
        'name',
        'slug',
        'level',
        'description',
    ];

    /**
     * Relation to table levels
     *
     * @return Modules\Company\Entities\Position
     */
    public function positions()
    {
        return $this->hasMany(Position::class, 'position_level_id', 'id');
    }
}
