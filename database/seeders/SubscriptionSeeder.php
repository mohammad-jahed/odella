<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('subscriptions')->insert([
            [
                'name_ar' => 'شهر',
                'name_en' => 'Month',
                'daysNumber' => '6',
                'price' => '600000'
            ],
            [
                'name_ar' => 'شهر',
                'name_en' => 'Month',
                'daysNumber' => '5',
                'price' => '500000'
            ],
            [
                'name_ar' => 'شهر',
                'name_en' => 'Month',
                'daysNumber' => '4',
                'price' => '400000'
            ],
            [
                'name_ar' => 'شهر',
                'name_en' => 'Month',
                'daysNumber' => '3',
                'price' => '300000'
            ],
            [
                'name_ar' => 'شهر',
                'name_en' => 'Month',
                'daysNumber' => '2',
                'price' => '200000'
            ],
            [
                'name_ar' => 'شهر',
                'name_en' => 'Month',
                'daysNumber' => '1',
                'price' => '100000'
            ],
            [
                'name_ar' => 'فصل',
                'name_en' => 'Semester',
                'daysNumber' => '6',
                'price' => '1500000'
            ],
            [
                'name_ar' => 'فصل',
                'name_en' => 'Semester',
                'daysNumber' => '5',
                'price' => '1400000'
            ],
            [
                'name_ar' => 'فصل',
                'name_en' => 'Semester',
                'daysNumber' => '4',
                'price' => '1300000'
            ],
            [
                'name_ar' => 'فصل',
                'name_en' => 'Semester',
                'daysNumber' => '3',
                'price' => '1300000'
            ],
            [
                'name_ar' => 'فصل',
                'name_en' => 'Semester',
                'daysNumber' => '2',
                'price' => '1200000'
            ],
            [
                'name_ar' => 'فصل',
                'name_en' => 'Semester',
                'daysNumber' => '1',
                'price' => '1000000'
            ],
        ]);
    }
}
