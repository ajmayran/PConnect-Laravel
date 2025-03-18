<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReturnRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_request_id',
        'order_detail_id',
        'quantity'
    ];

    /**
     * Get the return request this item belongs to.
     */
    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class);
    }

    /**
     * Get the order detail this return item is for.
     */
    public function orderDetail()
    {
        return $this->belongsTo(OrderDetails::class, 'order_detail_id');
    }
}