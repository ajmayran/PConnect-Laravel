<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'retailer_id',
        'distributor_id',
        'reason',
        'details',
        'status',
        'notes',
    ];

    public function retailer()
    {
        return $this->belongsTo(User::class, 'retailer_id');
    }

    public function distributor()
    {
        return $this->belongsTo(Distributors::class, 'distributor_id');
    }
}