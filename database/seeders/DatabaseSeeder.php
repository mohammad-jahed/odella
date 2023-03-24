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
        $this->call(UserSeeder::class);
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
