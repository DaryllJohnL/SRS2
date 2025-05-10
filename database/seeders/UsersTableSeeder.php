<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            'first_name' => 'Dha',
            'middle_name' => 'A.',
            'last_name' => 'Mariel',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'), // Always hash the password!
            'role_id' => 1, // Assuming Admin role has ID 1
            'department_id' => 1, // Assuming Accounting department has ID 1
            'created_by' => null, // Admin created by system
            'created_date' => now(),
            'last_update_date' => now(),
        ]);
    }
}
