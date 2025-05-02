<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'subtotal',
        'discount_amount',
        'free_items',
        'applied_discount',
        'delivery_address'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function itemDeliveries()
    {
        return $this->hasMany(OrderItemDelivery::class);
    }

    public function orderItemDelivery()
    {
        // This returns the first related OrderItemDelivery
        return $this->hasOne(OrderItemDelivery::class, 'order_details_id');
    }

    public function orderItemDeliveries()
    {
        // This returns all related OrderItemDelivery records
        return $this->hasMany(OrderItemDelivery::class, 'order_details_id');
    }
}
