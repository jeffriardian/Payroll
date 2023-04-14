<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Modules\Employee\Entities\Contact;
use Modules\Employee\Entities\Employee;
use Modules\General\Entities\Contact as GeneralContact;

$factory->define(Contact::class, function (Faker $faker) {
    return [
        'employee_nik' => factory(Employee::class),
        'general_contact_id' => factory(GeneralContact::class),
        'is_primary' => $faker->boolean(),
    ];
});
