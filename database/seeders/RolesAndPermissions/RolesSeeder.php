<?php

namespace Database\Seeders\RolesAndPermissions;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            ['name' => 'Admin', 'guard_name' => 'api'],
            ['name' => 'Employee', 'guard_name' => 'api'],
            ['name' => 'Supervisor', 'guard_name' => 'api'],
            ['name' => 'Student', 'guard_name' => 'api'],


        ]);
    }
}
