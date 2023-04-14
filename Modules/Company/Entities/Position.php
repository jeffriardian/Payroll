<?php

namespace Modules\Company\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Recruitment\Entities\PersonalDataCandidate;

class Position extends Model
{
    public $incrementing = false;

    protected $primaryKey = 'code';

    protected $table = 'company_positions';

    protected $fillable = [
        'code',
        'company_id',
        'position_level_id',
        'name',
        'slug',
    ];

    /**
     * Relation to table companies
     *
     * @return Modules\Company\Entities\Company
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relation to table levels
     *
     * @return Modules\Company\Entities\PositionLevel
     */
    public function level()
    {
        return $this->belongsTo(PositionLevel::class, 'position_level_id', 'id');
    }

    public function PersonalDataCandidate()
    {
        return $this->hasMany(PersonalDataCandidate::class, 'company_position_code', 'code');
    }
}
