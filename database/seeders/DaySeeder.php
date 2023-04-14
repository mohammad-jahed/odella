<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('days')->insert([
            ['name_ar' => 'الاثنين', 'name_en' => 'Monday'],
            ['name_ar' => 'الثلاثاء', 'name_en' => 'Tuesday'],
            ['name_ar' => 'الاربعاء', 'name_en' => 'Wednesday'],
            ['name_ar' => 'الخميس', 'name_en' => 'Wednesday'],
            ['name_ar' => 'الجمعة', 'name_en' => 'Friday'],
            ['name_ar' => 'السبت', 'name_en' => 'Saturday'],
            ['name_ar' => 'الاحد', 'name_en' => 'Sunday'],
        ]);
    }
}
