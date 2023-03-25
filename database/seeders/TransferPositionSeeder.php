<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransferPositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table("transfer_positions")->insert([
            [
                'name_ar'=>'المزة1',
                'name_en'=>'Al Mazzah1',
            ],
            [
                'name_ar'=>'المزة2',
                'name_en'=>'Al Mazzah2',
            ],
            [
                'name_ar'=>'جرمانا',
                'name_en'=>'Jaramanah',
            ],
        ]);

        DB::table('shared_positions')->insert([
            [
                'transportation_line_id'=> '1',
                'transfer_position_id'=>'1'
            ],
            [
                'transportation_line_id'=> '1',
                'transfer_position_id'=>'2'
            ],
            [
                'transportation_line_id'=> '1',
                'transfer_position_id'=>'3'
            ],
        ]);
    }
}
