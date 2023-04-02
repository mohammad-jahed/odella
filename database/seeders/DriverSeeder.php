<?php

namespace Database\Seeders;

use App\Models\Driver;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            'firstname'=>'ffff',
            'lastname'=>'llll',
            'number'=>123456789,
        ]);
        $driver = Driver::where('id',1)->first();
        $driver->buses()->attach(1);
    }
}
