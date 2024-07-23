<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Location::count()) {
            Location::truncate();
        }

        $Locations = [
            ['name' => 'دفتر شرکت'],
            ['name' => 'کارگاه چوب'],
            ['name' => 'پروژه A'],
            ['name' => 'پروژه B'],
            ['name' => 'پروژه C'],
            ['name' => 'پروژه D'],
        ];

        foreach ($Locations as $Location) {
            Location::create($Location);

        }
        $this->command->info('add Locations to database');
    }
}
