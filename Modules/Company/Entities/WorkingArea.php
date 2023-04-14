<?php

namespace Modules\Company\Entities;

use Illuminate\Database\Eloquent\Model;

class WorkingArea extends Model
{
    public $incrementing = false;

    protected $primaryKey = 'code';

    protected $table = 'company_working_areas';

    protected $fillable = [
        'code',
        'company_id',
        'parent_id',
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
     * Relation to self
     *
     * @return Modules\Company\Entities\WorkingArea
     */
    public function child()
    {
        return $this->hasMany(WorkingArea::class, 'parent_id', 'id');
    }

    /**
     * Relation to self
     *
     * @return Modules\Company\Entities\WorkingArea
     */
    public function parent()
    {
        return $this->belongsTo(WorkingArea::class, 'parent_id', 'id');
    }

    public function PersonalDataCandidate()
    {
        return $this->hasMany(PersonalDataCandidate::class, 'company_working_area_code', 'code');
    }
}
