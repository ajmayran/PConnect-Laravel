<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['
        order_id', 
        'distribution_id', 
        'payment_status', 
        'paid_at', 
        'payment_note'];
}