<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin User
        DB::table('system_user')->insertOrIgnore([
            'User_ID'       => 1,
            'Username'      => 'admin',
            'Password_Hash' => Hash::make('password'),
            'Role'          => 'admin',
            'Full_Name'     => 'System Administrator',
            'Is_Active'     => 1,
        ]);

        // 2. Incident Categories
        DB::table('incident_types')->insertOrIgnore([
            ['Category_Id' => 1, 'Category' => 'Noise Complaint'],
            ['Category_Id' => 2, 'Category' => 'Property Boundary Dispute'],
            ['Category_Id' => 3, 'Category' => 'Physical Altercation'],
            ['Category_Id' => 4, 'Category' => 'Vandalism / Property Damage'],
            ['Category_Id' => 5, 'Category' => 'Curfew Violation'],
        ]);
    }
}