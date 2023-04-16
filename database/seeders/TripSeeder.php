<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TripSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('trips')->insert([
            [
                'supervisor_id' => 1,
                'bus_driver_id' => 1,
                'line_id' => 1,
                'start' => "08:00",
                "date" => "2023-04-02",
                "position_ids" => array(1, 2, 3),
                "time" => array("08:15", "08:15", "08:15"),
                "status" => 1
            ],
            [
                'supervisor_id' => 1,
                'bus_driver_id' => 1,
                'line_id' => 1,
                'start' => "12:00",
                "date" => "2023-04-02",
                "position_ids" => array(1, 2, 3),
                "time" => array("08:15", "08:15", "08:15"),
                "status" => 2
            ],
            [
                'supervisor_id' => 1,
                'bus_driver_id' => 1,
                'line_id' => 1,
                'start' => "08:00",
                "date" => "2023-04-03",
                "position_ids" => array(1, 2, 3),
                "time" => array("08:15", "08:15", "08:15"),
                "status" => 1
            ],
            [
                'supervisor_id' => 1,
                'bus_driver_id' => 1,
                'line_id' => 1,
                'start' => "12:00",
                "date" => "2023-04-03",
                "position_ids" => array(1, 2, 3),
                "time" => array("08:15", "08:15", "08:15"),
                "status" => 2
            ],
        ]);
    }
}
