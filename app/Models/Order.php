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
        'cancel_reason',
    ];

    protected $casts = [
        'status_updated_at' => 'datetime',
    ];

    public function getFormattedOrderIdAttribute()
    {
        return sprintf(
            'ORD-%s-%s',
            $this->created_at->format('Ymd-His'),
            str_pad($this->id, 3, '0', STR_PAD_LEFT)
        );
    }

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

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
    
    public function returnRequests()
    {
        return $this->hasMany(\App\Models\ReturnRequest::class);
    }
}
