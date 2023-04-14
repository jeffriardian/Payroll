<?php

namespace Modules\Company\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Company\Entities\WorkingArea;

class ProductionWorkingAreaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        factory(WorkingArea::class, 10)->create();
        factory(WorkingArea::class, 2)->state('child')->create();
        factory(WorkingArea::class, 5)->state('child')->create([
            'parent_id' => 3,
        ]);
    }
}
