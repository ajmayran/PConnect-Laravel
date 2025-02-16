<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Order extends Model
{
    protected $fillable = [
        'user_id',
        'distributor_id',
        'status',
        'payment_status',
        'status_updated_at',
        'reject_reason',
    ];

    protected $casts = [
        'status_updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function distributor()
    {
        return $this->belongsTo(Distributors::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class);
    }

    public function delivery()
    {
        return $this->hasOne(Delivery::class);
    }
}
