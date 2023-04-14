<?php

namespace Modules\Employee\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Auth\Entities\User;
use Modules\Employee\Entities\Address;
use Modules\Employee\Entities\Contact;
use Modules\Employee\Entities\Employee;
use Modules\Employee\Entities\Identity;
use Modules\Employee\Entities\WorkingArea;

class EmployeeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        factory(Employee::class, 25)
            ->create()
            ->each(function ($employee) {
                $employee->addresses()->save(factory(Address::class)->make(['employee_nik' => $employee['nik']]));
                $employee->contacts()->save(factory(Contact::class)->make(['employee_nik' => $employee['nik']]));
                $employee->identity()->save(factory(Identity::class)->make(['employee_nik' => $employee['nik']]));
                $employee->identity()->save(factory(WorkingArea::class)->make(['employee_nik' => $employee['nik']]));
            });
    }
}
