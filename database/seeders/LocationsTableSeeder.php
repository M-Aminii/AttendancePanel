<?php

namespace Database\Seeders;

use App\Enums\LocationStatus;
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
            ['user_id'=>1,'name' => 'دفتر شرکت','status'=>LocationStatus::ACTIVE],
            ['user_id'=>1,'name' => 'کارگاه چوب','status'=>LocationStatus::ACTIVE],
            ['user_id'=>1,'name' => 'پروژه A','status'=>LocationStatus::ACTIVE],
            ['user_id'=>1,'name' => 'پروژه B','status'=>LocationStatus::ACTIVE],
            ['user_id'=>1,'name' => 'پروژه C','status'=>LocationStatus::ACTIVE],
            ['user_id'=>1,'name' => 'پروژه D','status'=>LocationStatus::ACTIVE],
        ];

        foreach ($Locations as $Location) {
            Location::create($Location);

        }
        $this->command->info('add Locations to database');
    }
}
