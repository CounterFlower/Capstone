<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (Schema::hasTable('system_user')) {
            DB::table('system_user')->insertOrIgnore([
                'User_ID' => 1,
                'Username' => 'test-admin',
                'Password_Hash' => 'test',
                'Role' => 'admin',
                'Full_Name' => 'Test Administrator',
                'Is_Active' => 1,
            ]);
        }

        if (Schema::hasTable('household')) {
            DB::table('household')->insertOrIgnore([
                'Household_Index' => 1,
                'Household_Id' => 1000001,
                'House_Number' => '1',
                'Zone_Purok' => 'Purok 1',
            ]);
        }
    }
}
