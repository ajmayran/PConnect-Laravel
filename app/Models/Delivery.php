<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Delivery extends Model
{
    protected $fillable = [
        'order_id',
        'truck_id',
        'address_id',
        'tracking_number',
        'estimated_delivery',
        'status',
        'exchange_for_return_id',
        'is_exchange_delivery',
        'delivered_at'
    ];

    protected $attributes = [
        'status' => 'pending',
    ];

    public static $statuses = [
        'pending',
        'in_transit',
        'out_for_delivery',
        'delivered',
        'failed'

    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($delivery) {
            if (empty($delivery->tracking_number)) {
                $delivery->tracking_number = strtoupper(Str::random(6)) . rand(100, 999);
            }
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function trucks()
    {
        return $this->belongsToMany(Trucks::class, 'truck_delivery', 'delivery_id', 'truck_id')
            ->withPivot('started_at')
            ->withTimestamps();
    }

    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class, 'exchange_for_return_id');
    }

    public function itemDeliveries()
    {
        return $this->hasMany(OrderItemDelivery::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }
}
