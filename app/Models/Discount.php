<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
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
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'percentage' => 'decimal:2',
    ];

    // Relationship with Distributor
    public function distributor()
    {
        return $this->belongsTo(Distributors::class, 'distributor_id');
    }

    // Relationship with Products
    public function products()
    {
        return $this->belongsToMany(Product::class, 'discount_product');
    }

    // Check if discount is valid based on dates
    public function isValid()
    {
        $now = now();
        Log::info('Checking discount validity', [
            'now' => $now,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_active' => $this->is_active,
        ]);

        return $this->is_active && $now->gte($this->start_date) && $now->lte($this->end_date);
    }

    // Calculate discount amount for percentage discounts
    public function calculatePercentageDiscount($price)
    {
        if ($this->type !== 'percentage' || !$this->isValid()) {
            return 0;
        }

        return ($price * $this->percentage) / 100;
    }

    // Calculate free items for freebie discounts
    public function calculateFreeItems($quantity)
    {
        if (
            $this->type !== 'freebie' || !$this->isValid() ||
            $this->buy_quantity <= 0 || $this->free_quantity <= 0
        ) {
            return 0;
        }

        // Calculate how many complete sets of "buy X" are in the cart
        $sets = floor($quantity / $this->buy_quantity);

        // Calculate free items
        return $sets * $this->free_quantity;
    }
}
