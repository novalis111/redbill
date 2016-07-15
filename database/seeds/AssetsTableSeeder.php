<?php

use Illuminate\Database\Seeder;

class AssetsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment('local')) {
            factory(Redbill\Asset::class, 1000)->create();
        }
    }
}
