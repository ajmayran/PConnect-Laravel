<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Discount;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckExpiredDiscounts extends Command
{
    protected $signature = 'discounts:check-expired';
    protected $description = 'Check and update expired discount statuses';

    public function handle()
    {
        $now = Carbon::now();
        
        $this->info("Checking for expired discounts at {$now->format('Y-m-d H:i:s')}...");
        
        // Find all discounts that have ended but aren't marked as expired
        $expiredDiscounts = Discount::where('end_date', '<', $now)
            ->where('status', '!=', 'expired')
            ->get();
            
        $count = $expiredDiscounts->count();
        $this->info("Found {$count} discounts to mark as expired");
        
        foreach ($expiredDiscounts as $discount) {
            $discount->status = 'expired';
            $discount->save();
            
            $this->line("- Marked discount '{$discount->name}' (ID: {$discount->id}) as expired");
            Log::info('Discount marked as expired:', [
                'discount_id' => $discount->id,
                'name' => $discount->name,
                'end_date' => $discount->end_date->format('Y-m-d H:i:s')
            ]);
        }
        
        $this->info("Completed marking expired discounts");
        
        return 0;
    }
}