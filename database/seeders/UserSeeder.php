<?php

namespace Database\Seeders;


use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('users')->insert([
           [
               'firstName' => 'admin',
               'lastName' => 'admin',
               'email' => 'admin@gmail.com',
               'password'=>'00000000',
               'phoneNumber'=>'0990312386'
           ]
        ]);
        $user = User::query()->where('email','admin@gmail.com')->first();
        $role = Role::query()->where('name', 'like', 'Admin')->get();
        $user->assignRole($role);
    }
}
