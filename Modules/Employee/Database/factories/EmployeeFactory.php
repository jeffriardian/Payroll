<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Modules\Company\Entities\Company;
use Modules\Employee\Entities\Employee;

$factory->define(Employee::class, function (Faker $faker) {
    return [
        'nik' => $faker->unique()->randomNumber,
        'company_id' => 1,
        'join_date' => $faker->date('Y-m-d'),
        'end_data' => $faker->date('Y-m-d'),
        'identity_card_number'=> '141.41242.1244.124',
        'tax_id_number' => '123.13233.3123.3123',
    ];
});
