<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Modules\Company\Entities\Company;

$factory->define(Company::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'nickname' => $faker->company,
        'vision' => $faker->text(250),
        'mission' => $faker->text(250),
        'moto' => $faker->text(100),
        'history' => $faker->text(),
        'founded_date' => $faker->date('Y-m-d', 'now'),
    ];
});
