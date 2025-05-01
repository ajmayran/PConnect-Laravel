<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Distributors;
use App\Models\Address;

class DistributorSeeder extends Seeder
{
    public function run()
    {
        // Create a test user
        $user = User::create([
            'first_name' => 'Test',
            'last_name' => 'Distributor',
            'email' => 'test@sample.com',
            'password' => bcrypt('password'),
            'user_type' => 'distributor',
            'profile_completed' => true, // Set profile_completed to true
            'status' => 'approved', // Set status to approved
            'email_verified_at' => now(), 
        ]);

        // Create a corresponding distributor record
        $distributor = Distributors::create([
            'user_id' => $user->id,
            'company_name' => 'Test Company',
            'company_email' => 'testcompany@example.com',
            'company_profile_image' => '', // Set to empty string for testing
            'company_phone_number' => '1234567890'
        ]);

        // Create an address for the distributor
        $address = new Address([
            'region' => '09', // Region IX - Zamboanga Peninsula
            'province' => '097300', // Zamboanga del Sur
            'city' => '093170', // City of Zamboanga
            'barangay' => '09317001', // Ayala barangay 
            'street' => 'Test Street, Building 123',
            'is_default' => true,
            'label' => 'Company Address'
        ]);

        // Save address using the relationship
        $distributor->addresses()->save($address);
    }
}