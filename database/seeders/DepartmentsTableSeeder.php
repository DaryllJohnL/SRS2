<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('departments')->insert([
            ['name' => 'Accounting'],
            ['name' => 'IT'],
            ['name' => 'Sales'],
            ['name' => 'HR'],
            ['name' => 'Logistics'],
        ]);
    }
}
