<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemDelivery extends Model
{
    protected $fillable = [
        'order_detail_id',
        'delivery_id',
        'quantity',
    ];

    public function orderDetail()
    {
        return $this->belongsTo(OrderDetails::class);
    }

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }
}

