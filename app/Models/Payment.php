<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'distributor_id',
        'payment_status',
        'paid_at',
        'payment_note'
    ];

    protected $dates = [
        'paid_at'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function earning()
    {
        return $this->hasOne(Earning::class);
    }
}
