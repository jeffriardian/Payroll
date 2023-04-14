<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Modules\Employee\Entities\Address;
use Modules\Employee\Entities\Employee;
use Modules\General\Entities\Address as GeneralAddress;

$factory->define(Address::class, function (Faker $faker) {
    return [
        'employee_nik' => factory(Employee::class),
        'general_address_id' => factory(GeneralAddress::class),
        'address_type' => random_int(1, 2),
    ];
});
