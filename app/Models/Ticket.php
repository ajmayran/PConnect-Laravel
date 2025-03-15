<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject',
        'content',
        'status',
        'rejection_reason',
        'image', 
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function retailerProfile()
    {
        return $this->hasOne(RetailerProfile::class);
    }

    public function retailer()
    {
        return $this->hasOne(RetailerProfile::class, 'user_id');
    }

    public function credential()
    {
        return $this->hasOne(Credential::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'distributor_id');
    }

    public function retailers()
    {
        return $this->belongsToMany(Retailers::class, 'retailer_profile', 'ticket_id', 'retailer_id');
    }
}