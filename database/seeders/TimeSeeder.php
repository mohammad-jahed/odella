<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
                'end' => '13:00',
                'date' => '2023-4-1'
            ],
            [
                'start' => '07:30',
                'end' => '13:30',
                'date' => '2023-4-2'
            ],
            [
                'start' => '08:00',
                'end' => '14:00',
                'date' => '2023-4-3'
            ],
            [
                'start' => '08:30',
                'end' => '14:30',
                'date' => '2023-4-4'
            ],
            [
                'start' => '09:00',
                'end' => '15:00',
                'date' => '2023-4-5'
            ],
            [
                'start' => '10:00',
                'end' => '16:00',
                'date' => '2023-4-6'
            ]
        ]);
    }
}
