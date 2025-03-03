<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Distributors;

class DistributorSeeder extends Seeder
{
    public function run()
    {
        // Create a test user
        $user = User::create([
            'first_name' => 'Test22',
            'last_name' => 'Distributor',
            'email' => 'test22@dist.com',
            'password' => bcrypt('password'),
            'user_type' => 'distributor',
            'profile_completed' => true, // Set profile_completed to true
            'status' => 'approved', // Set status to approved
        ]);

        // Create a corresponding distributor record
        Distributors::create([
            'user_id' => $user->id,
            'company_name' => 'Test Company22',
            'company_email' => 'testcompany22@example.com',
            'company_profile_image' => '', // Set to empty string for testing
            'region' => '',
            'province' => '',
            'city' => '',
            'barangay' => 'Test22 Barangay',
            'street' => 'Test22 Street',
            'company_phone_number' => '1234567890',
            'profile_completed' => false, // Initially set to false for testing
        ]);
    }
}
