<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Run the command to check for expired discounts daily at midnight
        $schedule->command('discounts:check-expired')->daily();
        
        // Run the command to check for expiring product batches daily at 8 AM
        $schedule->command('batches:check-expiring')->dailyAt('8:00');
        
        // Check for low stock items every 6 hours
        $schedule->command('inventory:check-low-stock')->everyFourHours();
        
        // Database maintenance tasks (weekly on Sunday at 1 AM)
        $schedule->command('db:backup')->weekly()->sundays()->at('1:00');

        $schedule->command('batches:check-expiring')->dailyAt('8:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    
}