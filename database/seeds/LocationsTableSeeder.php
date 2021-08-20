<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('locations')->insert([
            [
                'name'      => 'Cape Town',
                'slug'      => 'cape-town',
                'latitude'  => -33.8867171,
                'longitude' => 18.4960196
            ],

            [
                'name'      => 'Johannesburg',
                'slug'      => 'johannesburg',
                'latitude'  => -26.1715173,
                'longitude' => 28.0050051
            ]
        ]);
    }
}
