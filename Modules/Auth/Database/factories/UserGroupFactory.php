<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Modules\Auth\Entities\UserGroup;

$factory->define(UserGroup::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->name,
        'description' => $faker->text(100),
        'is_active' => 1,
    ];
});
