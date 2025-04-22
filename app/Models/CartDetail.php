<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartDetail extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'price',
        'quantity',
        'subtotal',
        'discount_amount', 
        'free_items',      
        'applied_discount'
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function distributor()
{
    return $this->belongsTo(Distributors::class, 'distributor_id');
}

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cartDetail) {
            $cartDetail->subtotal = $cartDetail->quantity * $cartDetail->product->price;
        });

        static::updating(function ($cartDetail) {
            $cartDetail->subtotal = $cartDetail->quantity * $cartDetail->product->price;
        });
    }
}