<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Earning extends Model
{
    protected $fillable = [
        'payment_id',
        'distributor_id',
        'amount'
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function distributor()
    {
        return $this->belongsTo(Distributors::class);
    }
}