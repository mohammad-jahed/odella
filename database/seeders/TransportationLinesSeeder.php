<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
                'name_ar'=>'المزة',
                'name_en'=>'Al Mazzah',
            ],
            [
                'name_ar'=>'المساكن',
                'name_en'=>'Al Masaken',
            ],
            [
                'name_ar'=>'جرمانا',
                'name_en'=>'Jaramanah',
            ],
        ]);
    }
}
