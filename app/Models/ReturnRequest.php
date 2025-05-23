<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReturnRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'retailer_id',
        'reason',
        'proof_image',
        'preferred_solution',
        'status',
        'processed_at',
        'reject_reason'
    ];

    protected $casts = [
        'proof_images' => 'array', // Cast the proof_images column as an array
    ];

    /**
     * Get the order associated with the return request.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the retailer who submitted the return request.
     */
    public function retailer()
    {
        return $this->belongsTo(User::class, 'retailer_id');
    }

    /**
     * Get the items included in this return request.
     */
    public function items()
    {
        return $this->hasMany(ReturnRequestItem::class);
    }
}