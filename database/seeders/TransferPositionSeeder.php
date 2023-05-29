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
                'name_ar' => 'دوار الجرة',
                'name_en' => 'Dawar Aljarah',
                "lng" => 36.27316785044969,
                "lat" => 33.49440777515016,
            ],
            [
                'name_ar' => 'مول كفرسوسة',
                'name_en' => 'Kafarsosah Mol',
                "lng" => 33.500992665543926,
                "lat" => 36.27419245429338,
            ],
            [
                'name_ar' => 'المواساة',
                'name_en' => 'Almouasat',
                "lng" => 36.30261674523354,
                "lat" => 33.624697112597936,
            ],
            [
                'name_ar' => 'برج تالا',
                'name_en' => 'Tala Tower',
                "lng" => 36.24377922154964,
                "lat" => 33.49684585342231,
            ],
            [
                'name_ar' => 'الجلاء',
                'name_en' => 'Aljalaa',
                "lng" => 36.250098505988724,
                "lat" => 33.49854128389337,
            ],
        ]);

        DB::table('shared_positions')->insert([
            [
                'transportation_line_id' => '1',
                'transfer_position_id' => '1'
            ],
            [
                'transportation_line_id' => '1',
                'transfer_position_id' => '2'
            ],
            [
                'transportation_line_id' => '1',
                'transfer_position_id' => '3'
            ],
            [
                'transportation_line_id' => '1',
                'transfer_position_id' => '4'
            ],
            [
                'transportation_line_id' => '1',
                'transfer_position_id' => '5'
            ],
            [
                'transportation_line_id' => '2',
                'transfer_position_id' => '3'
            ],
            [
                'transportation_line_id' => '2',
                'transfer_position_id' => '4'
            ],
            [
                'transportation_line_id' => '3',
                'transfer_position_id' => '1'
            ],
            [
                'transportation_line_id' => '3',
                'transfer_position_id' => '2'
            ],
            [
                'transportation_line_id' => '3',
                'transfer_position_id' => '3'
            ],
        ]);
    }
}
