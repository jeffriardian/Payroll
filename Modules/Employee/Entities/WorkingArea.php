<?php

namespace Modules\Employee\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Company\Entities\Position;
use Modules\Company\Entities\WorkingArea as ModulesWorkingArea;

class WorkingArea extends Model
{
    protected $table = 'employee_working_areas';

    protected $fillable = [
        'employee_nik',
        'company_working_area_code',
        'company_position_code',
        'start_date',
        'end_date',
        'is_current_working_area',
    ];

    /**
     * Relation to table employees
     *
     * @return Modules\Emplotee\Entities\Employee
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_nik', 'nik');
    }

    /**
     * Relation to table employees
     *
     * @return Modules\Emplotee\Entities\Employee
     */
    public function companyWorkingArea()
    {
        return $this->belongsTo(ModulesWorkingArea::class, 'company_working_area_code', 'code');
    }

    /**
     * Relation to table employees
     *
     * @return Modules\Emplotee\Entities\Employee
     */
    public function companyPosition()
    {
        return $this->belongsTo(Position::class, 'company_position_code', 'code');
    }
}
