<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\ZamboangaSeeder;

class AddZamboangaData extends Command
{
    protected $signature = 'address:add-zamboanga';
    protected $description = 'Add Zamboanga Del Sur, Zamboanga City, and barangays to the database';

    public function handle()
    {
        $this->info('Adding Zamboanga data...');
        $this->call('db:seed', [
            '--class' => ZamboangaSeeder::class,
        ]);
        
        return Command::SUCCESS;
    }
}   