<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UniversitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('universities')->insert([
            ['name_ar' => 'الجامعة العربية الدولية', 'name_en' => 'Arab International University', 'shortcut' => 'AIU'],
            ['name_ar' => 'الجامعة السورية الخاصة', 'name_en' => 'Syrian Private University', 'shortcut' => 'SPU'],
            ['name_ar' => 'جامعة قاسيون الخاصة', 'name_en' => 'Qasyoun Private University', 'shortcut' => 'QPU'],
            ['name_ar' => 'جامعة اليرموك الخاصة', 'name_en' => 'Yarmouq Private University', 'shortcut' => 'YPU'],
            ['name_ar' => 'جامعة الحواش الخاصة', 'name_en' => 'Hwaash Private University', 'shortcut' => 'HPU'],
            ['name_ar' => 'جامعة الرشيد الدولية الخاصة', 'name_en' => 'Rashed Private University', 'shortcut' => 'RPU'],
            ['name_ar' => 'جامعة القلمون الدولية الخاصة', 'name_en' => 'Qalamoun Private University', 'shortcut' => 'UOK'],
        ]);
    }
}
