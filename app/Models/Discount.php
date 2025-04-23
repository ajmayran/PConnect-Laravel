<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'distributor_id',
        'name',
        'code',
        'type',
        'percentage',
        'buy_quantity',
        'free_quantity',
        'start_date',
        'end_date',
        'is_active',
        'status'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean'
    ];

    /**
     * Check if the discount is currently valid based on dates
     * 
     * @return bool
     */
    public function isValid()
    {
        $now = Carbon::now();
        return $now->between($this->start_date, $this->end_date);
    }

    /**
     * Check if the discount has expired
     * 
     * @return bool
     */
    public function isExpired()
    {
        return Carbon::now()->isAfter($this->end_date);
    }

    /**
     * Calculate the percentage discount for a given price
     * 
     * @param float $price
     * @return float
     */
    public function calculatePercentageDiscount($price)
    {
        if ($this->type !== 'percentage') return 0;
        return $price * $this->percentage / 100;
    }

    /**
     * Calculate how many free items should be given based on quantity purchased
     * 
     * @param int $quantity
     * @return int
     */
    public function calculateFreeItems($quantity)
    {
        if ($this->type !== 'freebie' || $this->buy_quantity <= 0) return 0;
        $sets = floor($quantity / $this->buy_quantity);
        return $sets * $this->free_quantity;
    }

    public function distributor()
    {
        return $this->belongsTo(Distributors::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
    
}