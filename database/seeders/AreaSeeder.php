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

        ]);
    }
}
