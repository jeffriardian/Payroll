<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Modules\Auth\Entities\User;
use Illuminate\Support\Facades\Hash;
use Modules\Employee\Entities\Employee;

$factory->define(User::class, function (Faker $faker) {
    return [
        'employee_nik' => factory(Employee::class),
        'user_group_id' => 1, // Super Admin
        'username' => $faker->userName,
        'email' => $faker->email,
        'avatar' => $faker->imageUrl(150, 150, null, true, 'AV'),
        'password' => Hash::make('password'),
    ];
});

$factory->state(User::class, 'banned', [
    'account_status' => User::BANNED,
]);
