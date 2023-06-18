<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('areas')->insert([
            [
                'city_id' => '2',
                'name_ar' => 'البلدية',
                'name_en' => 'Al Baladiah',
            ],
            [
                'city_id' => '2',
                'name_ar' => 'الروضة',
                'name_en' => 'Al Rawdah',
            ],
            [
                'city_id' => '2',
                'name_ar' => 'الخضر',
                'name_en' => 'Al Khouder',
            ],

            [
                'city_id' => '2',
                'name_ar' => 'البعث',
                'name_en' => 'Al Baeth',
            ],

            [
                'city_id' => '2',
                'name_ar' => 'القريات',
                'name_en' => 'Al Qrayaat',
            ],

            [
                'city_id' => '2',
                'name_ar' => 'الحمصي',
                'name_en' => 'Al Homse',
            ],

            [
                'city_id' => '4',
                'name_ar' => 'باب شرقي',
                'name_en' => 'Bab Sharqi',
            ],

            [
                'city_id' => '4',
                'name_ar' => 'باب توما',
                'name_en' => 'Bab Touma',
            ],

            [
                'city_id' => '4',
                'name_ar' => 'القيمرية',
                'name_en' => 'Al Qemariah',
            ],

            [
                'city_id' => '4',
                'name_ar' => 'الجامع الاموي',
                'name_en' => 'The Umayyad Mosque',
            ],

            [
                'city_id' => '4',
                'name_ar' => 'النوفرة',
                'name_en' => 'Al Noufrah',
            ],

        ]);
    }
}
