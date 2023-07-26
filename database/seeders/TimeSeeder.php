<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('times')->insert([
            [
                'start' => '07:00',
                'date' => '2023-4-1',
                'day' => 'Sunday'
            ],
            [
                'start' => '07:30',
                'date' => '2023-4-2',
                'day' => 'Monday'
            ],
            [
                'start' => '08:00',
                'date' => '2023-4-3',
                'day' => 'Tuesday'
            ],
            [
                'start' => '08:30',
                'date' => '2023-4-4',
                'day' => 'Wednesday'
            ],
            [
                'start' => '09:00',
                'date' => '2023-4-5',
                'day' => 'Thursday'
            ],
            [
                'start' => '10:00',
                'date' => '2023-4-6',
                'day' => 'Friday'
            ]
        ]);
    }
}
