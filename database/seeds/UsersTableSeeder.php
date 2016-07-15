<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment('local')) {
            DB::table('users')->insert(
                [
                    'name'     => 'Redbill Dev',
                    'email'    => 'dev@localhost.local',
                    'password' => bcrypt('dev'),
                ]
            );
            factory(Redbill\User::class, 5)->create();
        }
    }
}
