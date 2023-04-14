<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Carbon\Carbon;
use Faker\Generator as Faker;
use Modules\Employee\Entities\Employee;
use Modules\Employee\Entities\Identity;

$factory->define(Identity::class, function (Faker $faker) {
    return [
        'employee_nik' => factory(Employee::class),
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'place_of_birth' => 'Garut',
        'birthday' => Carbon::now()->subYears(random_int(15, 50)),
        'gender' => $faker->boolean,
        'marital_status' => random_int(1, 4),
    ];
});
