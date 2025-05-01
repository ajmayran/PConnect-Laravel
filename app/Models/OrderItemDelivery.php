<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemDelivery extends Model
{
    protected $fillable = [
        'order_details_id',
        'address_id',
        'delivery_id',
        'quantity',
    ];

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function orderDetail()
    {
        return $this->belongsTo(OrderDetails::class, 'order_details_id');
    }
}
