<?php

namespace Modules\Auth\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Entities\User;
use Modules\Employee\Entities\Address;
use Modules\Employee\Entities\Contact;
use Modules\Employee\Entities\Employee;
use Modules\Employee\Entities\Identity;
use Modules\Employee\Entities\WorkingArea;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Employee::class, 1)
            ->create()
            ->each(function ($employee) {
                $employee->identity()->save(factory(User::class)->make([
                    'employee_nik' => $employee['nik'],
                    'username' => 'gustira08',
                ]));
                $employee->contacts()->save(factory(Contact::class)->make(['employee_nik' => $employee['nik']]));
                $employee->identity()->save(factory(Identity::class)->make(['employee_nik' => $employee['nik']]));
                $employee->addresses()->save(factory(Address::class)->make(['employee_nik' => $employee['nik']]));
            });

        factory(Employee::class, 1)
            ->create()
            ->each(function ($employee) {
                $employee->identity()->save(factory(User::class)->make([
                    'employee_nik' => $employee['nik'],
                    'username' => 'jeffri',
                ]));
                $employee->contacts()->save(factory(Contact::class)->make(['employee_nik' => $employee['nik']]));
                $employee->identity()->save(factory(Identity::class)->make(['employee_nik' => $employee['nik']]));
                $employee->addresses()->save(factory(Address::class)->make(['employee_nik' => $employee['nik']]));
                $employee->identity()->save(factory(WorkingArea::class)->make(['employee_nik' => $employee['nik']]));
            });

        factory(Employee::class, 1)
            ->create()
            ->each(function ($employee) {
                $employee->identity()->save(factory(User::class)->make([
                    'employee_nik' => $employee['nik'],
                    'username' => 'superadmin',
                ]));
                $employee->contacts()->save(factory(Contact::class)->make(['employee_nik' => $employee['nik']]));
                $employee->identity()->save(factory(Identity::class)->make(['employee_nik' => $employee['nik']]));
                $employee->addresses()->save(factory(Address::class)->make(['employee_nik' => $employee['nik']]));
                $employee->identity()->save(factory(WorkingArea::class)->make(['employee_nik' => $employee['nik']]));
            });
    }
}
