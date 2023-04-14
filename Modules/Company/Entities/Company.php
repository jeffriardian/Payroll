<?php

namespace Modules\Company\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Entities\Employee;

class Company extends Model
{
    protected $table = 'companies';

    protected $fillable = [
        'name',
        'nickname',
        'vision',
        'mission',
        'moto',
        'history',
        'founded_date',
    ];

    /**
     * Relation to table company_working_areas
     *
     * @return Modules\Company\Entities\WorkingArea
     */
    public function workingArea()
    {
        return $this->hasMany(WorkingArea::class);
    }

    /**
     * Relation to table company_positions
     *
     * @return Modules\Company\Entities\Position
     */
    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    /**
     * Relation to table employees
     *
     * @return Modules\Employee\Entities\Employee
     */
    public function employees()
    {
        return $this->hasMany(Employee::class, 'company_id', 'id');
    }
}
