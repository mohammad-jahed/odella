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
        ]);
    }
}
