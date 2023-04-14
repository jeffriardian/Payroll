<?php

namespace Modules\Employee\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Auth\Entities\User;
use Modules\Company\Entities\Company;

class Employee extends Model
{
    public $incrementing = false;

    protected $primaryKey = 'nik';

    protected $fillable = [
        'nik',
        'company_id',
        'join_date',
        'end_date',
        'identity_card_number',
        'tax_id_number',
    ];

    /**
     * Relation to table levels
     *
     * @return Modules\Company\Entities\PositionLevel
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    /**
     * Relation to table employee_identites
     *
     * @return Modules\Emplotee\Entities\Identity
     */
    public function identity()
    {
        return $this->hasOne(Identity::class, 'employee_nik', 'nik');
    }

    /**
     * Relation to table employee_identites
     *
     * @return Modules\Emplotee\Entities\Identity
     */
    public function addresses()
    {
        return $this->hasMany(Address::class, 'employee_nik', 'nik');
    }

    /**
     * Relation to table employee_identites
     *
     * @return Modules\Emplotee\Entities\Identity
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class, 'employee_nik', 'nik');
    }

    /**
     * Relation to table employee_identites
     *
     * @return Modules\Emplotee\Entities\Identity
     */
    public function user()
    {
        return $this->hasOne(User::class, 'employee_nik', 'nik');
    }

    public function workingArea()
    {
        return $this->hasMany(WorkingArea::class, 'employee_nik', 'nik');
    }

    /**
     * Get first employee contact where has general contact type as phone
     */
    public function getPhoneAttribute()
    {
        return $this->getPhones()->first();
    }

    /**
     * Get employee contacts where has general contact type as phone
     */
    public function getPhones()
    {
        $this->load('contacts');

        return $this->contacts()
            ->with('generalContact')
            ->whereHas('generalContact', function ($query) {
                $query->where('contact_type_id', 1);
            })
            ->get();
    }

    public function getWorkingAreaAttribute()
    {
        return $this->getWorkingArea()->first();
    }

    public function getWorkingArea()
    {
        $this->load('workingArea');
        return $this->workingArea()->with('companyWorkingArea')
            ->where('is_current_working_area', 1)
            ->get();
    }
}
