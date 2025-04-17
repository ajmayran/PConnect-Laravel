<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistributorFollower extends Model
{
    protected $fillable = [
        'distributor_id',
        'retailer_id',
    ];

    public function distributor()
    {
        return $this->belongsTo(Distributors::class);
    }

    public function retailer()
    {
        return $this->belongsTo(User::class, 'retailer_id');
    }
}