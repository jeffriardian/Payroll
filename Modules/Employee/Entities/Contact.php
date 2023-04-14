<?php

namespace Modules\Employee\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\General\Entities\Contact as GeneralContact;

class Contact extends Model
{
    protected $table = 'employee_contacts';

    protected $fillable = [
        'employee_nik',
        'general_contact_id',
        'is_primary',
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
     * Relation to table general contact
     *
     * @return Modules\Emplotee\Entities\Employee
     */
    public function generalContact()
    {
        return $this->belongsTo(GeneralContact::class, 'general_contact_id', 'id');
    }
}
