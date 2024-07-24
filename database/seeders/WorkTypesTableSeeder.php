<?php

namespace Database\Seeders;

use App\Models\WorkType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (WorkType::count()) {
            WorkType::truncate();
        }

        $WorkTypes = [
            ['name' => 'نصب شیشه'],
            ['name' => 'نصب فرم'],
            ['name' => 'جوشکاری'],
            ['name' => 'تخلیه و بارگیری شیشه'],
            ['name' => 'رنگ کاری'],
            ['name' => 'سایر'],
        ];

        foreach ($WorkTypes as $WorkType) {
            WorkType::create($WorkType);

        }
        $this->command->info('add WorkTypes to database');
    }
}
