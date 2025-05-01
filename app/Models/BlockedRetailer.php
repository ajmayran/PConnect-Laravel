<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedRetailer extends Model
{
    use HasFactory;

    protected $fillable = [
        'distributor_id',
        'retailer_id',
        'reason',
    ];

    public function distributor()
    {
        return $this->belongsTo(User::class, 'distributor_id');
    }

    public function retailer()
    {
        return $this->belongsTo(User::class, 'retailer_id');
    }
}