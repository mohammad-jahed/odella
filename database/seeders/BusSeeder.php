<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('buses')->insert(
            [
                [
                    'key' => 'testKey1',
                    'capacity' => 30,
                ],
                [
                    'key' => 'testKey2',
                    'capacity' => 30,
                ],
                [
                    'key' => 'testKey3',
                    'capacity' => 30,
                ],
                [
                    'key' => 'testKey4',
                    'capacity' => 30,
                ],
                [
                    'key' => 'testKey5',
                    'capacity' => 30,
                ],
                [
                    'key' => 'testKey6',
                    'capacity' => 20,
                ],
                [
                    'key' => 'testKey7',
                    'capacity' => 20,
                ],
                [
                    'key' => 'testKey8',
                    'capacity' => 45,
                ],
                [
                    'key' => 'testKey9',
                    'capacity' => 45,
                ],
                [
                    'key' => 'testKey10',
                    'capacity' => 45,
                ],
                [
                    'key' => 'testKey11',
                    'capacity' => 45,
                ],
                [
                    'key' => 'testKey12',
                    'capacity' => 45,
                ],
                [
                    'key' => 'testKey13',
                    'capacity' => 45,
                ],
            ]
        );
    }
}
