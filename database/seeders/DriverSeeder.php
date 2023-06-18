<?php

namespace Database\Seeders;

use App\Models\Driver;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('drivers')->insert(
            [
                [
                    'firstname' => 'driver1',
                    'lastname' => 'driver1',
                    'number' => 123456789,
                ],
                [
                    'firstname' => 'driver2',
                    'lastname' => 'driver2',
                    'number' => 123456789,
                ],
                [
                    'firstname' => 'driver3',
                    'lastname' => 'driver3',
                    'number' => 123456789,
                ],
                [
                    'firstname' => 'driver4',
                    'lastname' => 'driver4',
                    'number' => 123456789,
                ],
                [
                    'firstname' => 'driver5',
                    'lastname' => 'driver5',
                    'number' => 123456789,
                ],
                [
                    'firstname' => 'driver6',
                    'lastname' => 'driver6',
                    'number' => 123456789,
                ],
                [
                    'firstname' => 'driver7',
                    'lastname' => 'driver7',
                    'number' => 123456789,
                ],
                [
                    'firstname' => 'driver8',
                    'lastname' => 'driver8',
                    'number' => 123456789,
                ],
                [
                    'firstname' => 'driver9',
                    'lastname' => 'driver9',
                    'number' => 123456789,
                ],
                [
                    'firstname' => 'driver10',
                    'lastname' => 'driver10',
                    'number' => 123456789,
                ],
                [
                    'firstname' => 'driver11',
                    'lastname' => 'driver11',
                    'number' => 123456789,
                ],
                [
                    'firstname' => 'driver12',
                    'lastname' => 'driver12',
                    'number' => 123456789,
                ],
                [
                    'firstname' => 'driver13',
                    'lastname' => 'driver13',
                    'number' => 123456789,
                ],
            ]
        );
        /**
         * @var Driver $driver1
         * @var Driver $driver2
         * @var Driver $driver3
         * @var Driver $driver4
         * @var Driver $driver5
         * @var Driver $driver6
         * @var Driver $driver7
         * @var Driver $driver8
         * @var Driver $driver9
         * @var Driver $driver10
         * @var Driver $driver11
         * @var Driver $driver12
         * @var Driver $driver13
         */
        $driver1 = Driver::query()->where('id', 1)->first();
        $driver1->buses()->attach(1);

        $driver2 = Driver::query()->where('id', 2)->first();
        $driver2->buses()->attach(2);

        $driver3 = Driver::query()->where('id', 3)->first();
        $driver3->buses()->attach(3);

        $driver4 = Driver::query()->where('id', 4)->first();
        $driver4->buses()->attach(4);

        $driver5 = Driver::query()->where('id', 5)->first();
        $driver5->buses()->attach(5);

        $driver6 = Driver::query()->where('id', 6)->first();
        $driver6->buses()->attach(6);

        $driver7 = Driver::query()->where('id', 7)->first();
        $driver7->buses()->attach(7);

        $driver8= Driver::query()->where('id', 8)->first();
        $driver8->buses()->attach(8);

        $driver9 = Driver::query()->where('id', 9)->first();
        $driver9->buses()->attach(9);

        $driver10 = Driver::query()->where('id', 10)->first();
        $driver10->buses()->attach(10);

        $driver11= Driver::query()->where('id', 11)->first();
        $driver11->buses()->attach(11);

        $driver12 = Driver::query()->where('id', 12)->first();
        $driver12->buses()->attach(12);

        $driver13 = Driver::query()->where('id', 13)->first();
        $driver13->buses()->attach(13);


    }
}
