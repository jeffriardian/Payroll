<?php

namespace Modules\Employee\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\General\Entities\Address as GeneralAddress;

class Address extends Model
{
    CONST AT_HOME_ADDRESS = 1;
    CONST AT_IDENTITY_ADDRESS = 2;

    protected $table = 'employee_addresses';

    protected $fillable = [
        'employee_nik',
        'general_address_id',
        'address_type',
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
     * Relation to table general address
     *
     * @return Modules\Emplotee\Entities\Employee
     */
    public function generalAddress()
    {
        return $this->belongsTo(GeneralAddress::class, 'general_address_id', 'id');
    }
}
