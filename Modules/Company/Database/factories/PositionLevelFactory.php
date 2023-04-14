<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Modules\Company\Entities\PositionLevel;

$factory->define(PositionLevel::class, function (Faker $faker) {
    $level = $faker->name;

    return [
        'name' => $level,
        'slug' => Str::slug($level, '-'),
        'level' => random_int(1, 5),
        'description' => null,
    ];
});
