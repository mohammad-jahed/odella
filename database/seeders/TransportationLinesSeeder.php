<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransportationLinesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table("transportation_lines")->insert([
            [
                'name_ar' => 'المزة',
                'name_en' => 'Al Mazzah',
            ],
            [
                'name_ar' => 'جرمانا',
                'name_en' => 'Jaramanah',
            ],
            [
                'name_ar' => 'حاميش _ الميسات',
                'name_en' => 'Hamesh _ Al Misaat',
            ],

            [
                'name_ar' => 'شارع بغداد',
                'name_en' => 'Bagdaad Street',
            ],




        ]);
    }
}
