<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DistributorSubscription extends Model
{
    protected $fillable = [
        'distributor_id',
        'plan',
        'amount',
        'payment_id',
        'checkout_id',
        'reference_number',
        'starts_at',
        'expires_at',
        'status'
    ];

    protected $dates = [
        'starts_at',
        'expires_at',
        'created_at',
        'updated_at'
    ];

    public function distributor()
    {
        return $this->belongsTo(Distributors::class, 'distributor_id');
    }

    public function getPlanNameAttribute()
    {
        return match($this->plan) {
            '3_months' => '3 Months',
            '6_months' => '6 Months',
            '1_year' => '1 Year',
            default => 'Unknown'
        };
    }
    
    public function getRemainingDaysAttribute()
    {
        if (!$this->expires_at || $this->status !== 'active') {
            return 0;
        }
        
        return max(0, Carbon::now()->diffInDays($this->expires_at, false));
    }
    
    public function isActive()
    {
        return $this->status === 'active' && 
               $this->expires_at && 
               Carbon::now()->lt($this->expires_at);
    }
}