<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetailerProfile extends Model
{
    protected $table = 'retailer_profile';

    protected $fillable = [
        'user_id',
        'business_name',
        'phone',
        'address',
        'profile_picture',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tickets()
{
    return $this->belongsToMany(Ticket::class, 'retailer_profile', 'retailer_id', 'ticket_id');
}
}
