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
        DB::table('drivers')->insert([
            'firstname' => 'driver1',
            'lastname' => 'driver',
            'number' => 123456789,
        ]);
        /**
         * @var Driver $driver
         */
        $driver = Driver::query()->where('id', 1)->first();

        $driver->buses()->attach(1);
    }
}
