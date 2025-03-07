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
            'email' => 'admin@example.com',
            'password' => bcrypt('password'), // Use a secure password
            'is_admin' => true, // Set is_admin to true
            'user_type' => 'admin', // Set user type to distributor
            'is_admin' => true, // Indicate that this user is an admin
        ]);

        $this->call([
            CategorySeeder::class,
        ]);
    }
    
}
