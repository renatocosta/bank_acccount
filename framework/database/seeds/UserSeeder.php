<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('users')->insert([
            [
                'id'      => 1,
                'name'      => 'Assaf',
                'username'  => 'assaf',
                'email' => 'assaf@turnoverbnb.com',
                'password' => '984y468752nSJK$59u3nÇ!0hsksk'
            ],
            [
                'id'      => 2,
                'name'      => 'Renato',
                'username'  => 'renato',
                'email' => 'renato@turnoverbnb.com',
                'password' => 'Ak984y#k68752#%%K$59u3nÇ!89#'
            ],
        ]);
    }
}
