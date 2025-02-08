<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Distributors;

class TestDistributorSeeder extends Seeder
{
    public function run()
    {
        // Create a test user
        $user = User::create([
            'first_name' => 'Test',
            'last_name' => 'Distributor',
            'email' => 'testdistributor@example.com',
            'password' => bcrypt('password123'),
            'user_type' => 'distributor',
            'status' => 'approved', // Set status to approved
        ]);

        // Create a corresponding distributor record
        Distributors::create([
            'user_id' => $user->id,
            'company_name' => 'Test Company',
            'company_email' => 'testcompany@example.com',
            'company_profile_image' => '', // Set to empty string for testing


            'company_address' => '123 Test St',
            'company_phone_number' => '1234567890',
            'profile_completed' => false, // Initially set to false for testing
        ]);
    }
}
