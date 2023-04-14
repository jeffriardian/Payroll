<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Modules\Company\Entities\WorkingArea;

$factory->define(WorkingArea::class, function (Faker $faker) {
    return [
        'code' => random_int(10000000, 99999999),
        'company_id' => 1,
        'parent_id' => null,
        'name' => $faker->text(10),
        'slug' => $faker->text(10),
    ];
});

$factory->state(WorkingArea::class, 'child', [
    'parent_id' => 1,
]);
