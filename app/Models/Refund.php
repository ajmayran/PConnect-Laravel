<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Refund extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_request_id',
        'order_id',
        'amount',
        'status',
        'processed_by',
        'processed_at',
        'scheduled_date',
        'completed_at'
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'scheduled_date' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}