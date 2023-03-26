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
//        $data = [
//            'location_id'=>1,
//            'firstName' => 'Admin',
//            'lastName' => 'Admin',
//            'email' => 'admin@admin.com',
//            'password' => 00000000,
//            'phoneNumber' => '12345678',
//        ];
//        $user = User::query()->create($data);
//        /////////////////////assign role to the Admin/////////////////////////////////
//        $role = Role::query()->where('name', 'like', 'Admin')->get();
//        $user->assignRole($role);

    }
}
