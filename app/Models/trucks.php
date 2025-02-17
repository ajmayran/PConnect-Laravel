<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trucks extends Model
{
    protected $fillable = [
        'distributor_id',
        'plate_number',
        'delivery_location',
        'status',
    ];

    protected $attributes = [
        'status' => 'available'
    ];

    public function distributor()
    {
        return $this->belongsTo(Distributors::class);
    }

    public function deliveries()
    {
        return $this->belongsToMany(Delivery::class, 'truck_delivery','truck_id', 'delivery_id')
            ->withPivot('started_at')
            ->withTimestamps();
    }
}
