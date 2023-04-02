<?php

namespace Database\Seeders;


use App\Enums\Status;
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
     //////////////////////////////////////////Admin////////////////////////////////////////////////
        $data = [
            'location_id'=>1,
            'firstName' => 'Admin',
            'lastName' => 'Admin',
            'email' => 'admin33@admin.com',
            'password' => Hash::make('00000000'),
            'phoneNumber' => '12345678',
            'status'=> Status::NonStudents
        ];

        $user = User::query()->create($data);
        /////////////////////assign role to the Admin/////////////////////////////////
        $role = Role::query()->where('name', 'like', 'Admin')->first();
        $user->assignRole($role);

     //////////////////////////////////////Supervisor//////////////////////////////////////////////////////

        $data = [
            'location_id'=>1,
            'firstName' => 'Supervisor',
            'lastName' => 'Supervisor',
            'email' => 'supervisor@supervisor.com',
            'password' => Hash::make('00000000'),
            'phoneNumber' => '12345678',
            'status'=> Status::NonStudents
        ];
        $user = User::query()->create($data);
        /////////////////////assign role to the Supervisor/////////////////////////////////
        $role = Role::query()->where('name', 'like', 'Supervisor')->first();
        $user->assignRole($role);



        //////////////////////////////////////Employee//////////////////////////////////////////////////////

        $data = [
            'location_id'=>1,
            'firstName' => 'Employee',
            'lastName' => 'Employee',
            'email' => 'employee@employee.com',
            'password' => Hash::make('00000000'),
            'phoneNumber' => '12345678',
            'status'=> Status::NonStudents
        ];
        $user = User::query()->create($data);
        /////////////////////assign role to the Employee/////////////////////////////////
        $role = Role::query()->where('name', 'like', 'Employee')->first();
        $user->assignRole($role);

    }
}
