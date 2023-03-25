<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\RolesAndPermissions\PermissionSeeder;
use Database\Seeders\RolesAndPermissions\RolesSeeder;
use Database\Seeders\RolesAndPermissions\RolHasPermissionSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(RolHasPermissionSeeder::class);
        $this->call(CitySeeder::class);
        $this->call(AreaSeeder::class);
        $this->call(SubscriptionSeeder::class);
        $this->call(LocationSeeder::class);
        $this->call(TransportationLinesSeeder::class);
        $this->call(TransferPositionSeeder::class);
        $this->call(UserSeeder::class);
    }
}
