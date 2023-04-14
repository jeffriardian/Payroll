<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Modules\Company\Entities\Position;

$factory->define(Position::class, function (Faker $faker) {
    $jobTitle = $faker->jobTitle;

    return [
        'code' => random_int(11111111, 99999999),
        'company_id' => 1,
        'position_level_id' => 1,
        'name' => $jobTitle,
        'slug' => Str::slug($jobTitle, '-'),
    ];
});
