<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('cities')->insert([
            [
                'name_ar' => 'المزة',
                'name_en' => 'Al Mazzah',
            ],
            [
                'name_ar' => 'جرمانا ',
                'name_en' => 'Jaramanah',
            ],
            [
                'name_ar' => 'الشعلان',
                'name_en' => 'Al shalaan',
            ],
        ]);
    }
}
