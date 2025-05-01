<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::factory()->create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@sample.com',
            'password' => bcrypt('password'), // Use a secure password
            'is_admin' => true, // Set is_admin to true
            'user_type' => 'admin', // Set user type to distributor
            'is_admin' => true, // Indicate that this user is an admin
            'email_verified_at' => now()
        ]);

        // Create a test user
        User::factory()->create([
            'first_name' => 'Sample',
            'last_name' => 'Retailer',
            'email' => 'sample@gmail.com',
            'password' => bcrypt('password'),
            'user_type' => 'Retailer',
            'profile_completed' => false, // Set profile_completed to true
            'status' => 'approved', // Set status to approved
            'email_verified_at' => now()
        ]);

        $this->call([
            CategorySeeder::class,
            DistributorSeeder::class
        ]);
    }
}
