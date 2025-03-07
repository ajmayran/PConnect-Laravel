<?php

use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('messaging:create-test-data', function () {
    $this->info('Creating test users and messages...');
    
    // Find or create a retailer
    $retailer = User::where('user_type', 'retailer')->first();
    if (!$retailer) {
        $retailer = User::create([
            'first_name' => 'Test',
            'last_name' => 'Retailer',
            'email' => 'test.retailer@example.com',
            'password' => bcrypt('password'),
            'user_type' => 'retailer'
        ]);
        $this->info('Created retailer: ' . $retailer->email);
    } else {
        $this->info('Using existing retailer: ' . $retailer->email);
    }
    
    // Find or create a distributor
    $distributor = User::where('user_type', 'distributor')
        ->where('status', 'approved')
        ->first();
        
    if (!$distributor) {
        $distributor = User::create([
            'first_name' => 'Test',
            'last_name' => 'Distributor',
            'email' => 'test.distributor@example.com',
            'password' => bcrypt('password'),
            'user_type' => 'distributor',
            'status' => 'approved',
            'profile_completed' => true
        ]);
        $this->info('Created distributor: ' . $distributor->email);
    } else {
        $this->info('Using existing distributor: ' . $distributor->email);
    }
    
    // Create some test messages
    $messages = [
        [
            'sender_id' => $retailer->id,
            'receiver_id' => $distributor->id,
            'message' => 'Hello! This is a test message from the retailer.',
            'is_read' => true
        ],
        [
            'sender_id' => $distributor->id,
            'receiver_id' => $retailer->id,
            'message' => 'Hi there! This is a response from the distributor.',
            'is_read' => true
        ],
        [
            'sender_id' => $retailer->id,
            'receiver_id' => $distributor->id,
            'message' => 'I need to order some products.',
            'is_read' => false
        ],
        [
            'sender_id' => $distributor->id,
            'receiver_id' => $retailer->id,
            'message' => 'Sure, what do you need?',
            'is_read' => false
        ],
    ];
    
    $createdCount = 0;
    foreach ($messages as $msg) {
        Message::create($msg);
        $createdCount++;
        $this->line('Created message: ' . $msg['message']);
    }
    
    $this->info("Successfully created $createdCount messages");
    
    $this->info("\nTest login credentials:");
    $this->line("Retailer: {$retailer->email} / password");
    $this->line("Distributor: {$distributor->email} / password");
})->purpose('Create test data for the messaging system');
