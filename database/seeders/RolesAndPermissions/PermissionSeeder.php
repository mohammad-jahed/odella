<?php

namespace Database\Seeders\RolesAndPermissions;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('permissions')->insert([
            //Admin
            ['name' => 'Add Subscription', 'guard_name' => 'api'],
            ['name' => 'delete Subscription', 'guard_name' => 'api'],
            ['name' => 'Update Subscription', 'guard_name' => 'api'],
            ['name' => 'Add Transportation_Line', 'guard_name' => 'api'],
            ['name' => 'Update Transportation_Line', 'guard_name' => 'api'],
            ['name' => 'Delete Transportation_Line', 'guard_name' => 'api'],
            ['name' => 'Add Position', 'guard_name' => 'api'],
            ['name' => 'Update Position', 'guard_name' => 'api'],
            ['name' => 'Delete Position', 'guard_name' => 'api'],
            ['name' => 'Add City', 'guard_name' => 'api'],
            ['name' => 'Update City', 'guard_name' => 'api'],
            ['name' => 'Delete City', 'guard_name' => 'api'],
            ['name' => 'Add Area', 'guard_name' => 'api'],
            ['name' => 'Update Area', 'guard_name' => 'api'],
            ['name' => 'Delete Area', 'guard_name' => 'api'],
            ['name' => 'Add Employee', 'guard_name' => 'api'],
            ['name' => 'Delete Employee', 'guard_name' => 'api'],
            ['name' => 'Add Supervisor', 'guard_name' => 'api'],
            ['name' => 'Delete Supervisor', 'guard_name' => 'api'],
            ['name' => 'Add Trip', 'guard_name' => 'api'],
            ['name' => 'Update Trip', 'guard_name' => 'api'],
            ['name' => 'Delete Trip', 'guard_name' => 'api'],
            ['name' => 'Add Driver', 'guard_name' => 'api'],
            ['name' => 'Update Driver', 'guard_name' => 'api'],
            ['name' => 'Delete Driver', 'guard_name' => 'api'],
            ['name' => 'View Drivers', 'guard_name' => 'api'],
            ['name' => 'Add Bus', 'guard_name' => 'api'],
            ['name' => 'Update Bus', 'guard_name' => 'api'],
            ['name' => 'Delete Bus', 'guard_name' => 'api'],
            ['name' => 'View Buses', 'guard_name' => 'api'],
            ['name' => 'View SupervisorEvaluation', 'guard_name' => 'api'],
            ['name' => 'Add Notification', 'guard_name' => 'api'],
            ['name' => 'Delete Notification', 'guard_name' => 'api'],
            ['name' => 'View Complain', 'guard_name' => 'api'],

            //Supervisor

            ['name' => 'Confirm Student Attendance', 'guard_name' => 'api'],
            ['name' => 'Confirm Daily Reservation', 'guard_name' => 'api'],
            ['name' => 'View Passengers', 'guard_name' => 'api'],
            ['name' => 'View Supervisor Program', 'guard_name' => 'api'],

            //Employee
            ['name' => 'Confirm registration', 'guard_name' => 'api'],
            ['name' => 'Add Student', 'guard_name' => 'api'],
            ['name' => 'Update Student', 'guard_name' => 'api'],
            ['name' => 'Delete Student', 'guard_name' => 'api'],
            ['name' => 'View Trips', 'guard_name' => 'api'],
            ['name' => 'View Driver', 'guard_name' => 'api'],

            //Student
            ['name' => 'View The Nearest Bus', 'guard_name' => 'api'],
            ['name' => 'Daily Reservation', 'guard_name' => 'api'],
            ['name' => 'View Student Program', 'guard_name' => 'api'],
            ['name' => 'Rating Supervisor', 'guard_name' => 'api'],
            ['name' => 'Add complain', 'guard_name' => 'api'],
            ['name' => 'Add Lost&Found', 'guard_name' => 'api'],
            ['name' => 'View Lost&Found', 'guard_name' => 'api'],


            ['name' => 'View Employee', 'guard_name' => 'api'],
            ['name' => 'Update Employee', 'guard_name' => 'api'],
            ['name' => 'View Supervisor', 'guard_name' => 'api'],
            ['name' => 'Update Supervisor', 'guard_name' => 'api'],
            ['name' => 'View Student', 'guard_name' => 'api'],
            ['name' => 'View Universities', 'guard_name' => 'api'],
            ['name' => 'Add University', 'guard_name' => 'api'],
            ['name' => 'Update University', 'guard_name' => 'api'],
            ['name' => 'Delete University', 'guard_name' => 'api'],
            ['name' => 'View Time', 'guard_name' => 'api'],
            ['name' => 'Add Time', 'guard_name' => 'api'],
            ['name' => 'Update Time', 'guard_name' => 'api'],
            ['name' => 'Delete Time', 'guard_name' => 'api'],
            ['name' => 'View Programs', 'guard_name' => 'api'],
        ]);
    }
}
