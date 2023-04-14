<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Modules\Company\Entities\Position;
use Modules\Company\Entities\WorkingArea as CompanyWorkingArea;
use Modules\Employee\Entities\Employee;
use Modules\Employee\Entities\WorkingArea;

$factory->define(WorkingArea::class, function (Faker $faker) {
    return [
        'employee_nik' => factory(Employee::class),
        'company_working_area_code' => factory(CompanyWorkingArea::class),
        'company_position_code' => factory(Position::class),
        'start_date' => $faker->date('Y-m-d'),
        'end_date' => $faker->date('Y-m-d'),
        'is_current_working_area' => $faker->boolean(),
    ];
});
