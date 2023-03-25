<?php

namespace Database\Seeders;


use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->create([
            'firstName' => 'Admin',
            'lastName' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('00000000'),
            'phoneNumber' => '12345678',
            'location_id' => 1,
            'subscription_id' => 1,
            'transportation_line_id' => 1,
            'transfer_position_id' => 1,
        ]);
        /////////////////////assign role to the Admin/////////////////////////////////
        $role = Role::query()->where('name', 'like', 'Admin')->get();
        $user->assignRole($role);

    }
}
