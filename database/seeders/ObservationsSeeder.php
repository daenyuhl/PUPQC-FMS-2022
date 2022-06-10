<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ObservationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Observation::factory(10)->create();
    }
}
