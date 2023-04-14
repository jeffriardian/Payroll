<?php

namespace Modules\Employee\Entities;

use Illuminate\Database\Eloquent\Model;

class Identity extends Model
{
    const MS_SINGLE = 1;
    const MS_MARRIED = 2;
    const MS_WIDOW = 3; // Janda
    const MS_WIDOWER = 4; // Duda

    protected $table = 'employee_identities';

    protected $fillable = [
        'employee_nik',
        'first_name',
        'last_name',
        'place_of_birth',
        'birthday',
        'gender',
        'marital_status',
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
}
