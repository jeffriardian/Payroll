<?php

namespace Modules\Auth\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Auth\Entities\UserGroup;

class UserGroupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $groups = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => '-'
            ],
            [
                'name' => 'Direksi',
                'slug' => 'direksi',
                'description' => '-'
            ],
            [
                'name' => 'Gudang Umum',
                'slug' => 'gudang-umum',
                'description' => '-'
            ],
            [
                'name' => 'Pembelian',
                'slug' => 'pembelian',
                'description' => '-'
            ]

       ];

       foreach ($groups as $group) {
        UserGroup::create($group);
       }
    }
}
